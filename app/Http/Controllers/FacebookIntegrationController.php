<?php

namespace App\Http\Controllers;

use App\Models\AssignedUser;
use App\Models\CampaignFieldMigration;
use App\Models\CampaignFields;
use App\Models\CampaignMaster;
use App\Models\FbuserMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class FacebookIntegrationController extends Controller
{
    public function index(Request $request)
    {
        $campaignMasters = CampaignMaster::query()
            ->with(['assignedUsers' => function ($query) {
                $query->select('id', 'campaign_id', 'user_id', 'user_name')
                    ->orderBy('user_name');
            }])
            ->when($request->filled('campaign_name'), function ($query) use ($request) {
                $query->where('campaign_name', 'like', '%' . trim($request->campaign_name) . '%');
            })
            ->when($request->filled('campaign_id'), function ($query) use ($request) {
                $campaignId = trim($request->campaign_id);

                $query->where(function ($subQuery) use ($campaignId) {
                    $subQuery->where('camp_id', 'like', '%' . $campaignId . '%')
                        ->orWhere('ad_id', 'like', '%' . $campaignId . '%');
                });
            })
            ->when($request->filled('assigned_user'), function ($query) use ($request) {
                $query->whereHas('assignedUsers', function ($assignedQuery) use ($request) {
                    $assignedQuery->where('user_id', $request->assigned_user);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $activeUsers = User::where('user_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.settings.facebook-integration.index', compact('campaignMasters', 'activeUsers'));
    }

    public function facebookIndex() {
        try {
            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.fb-integrate' )->with( 'success', 'page loaded successfully' )->render(),
            ] );
        } catch ( \Throwable $th ) {
            Log::error( $th );
            return redirect()->back()->with( 'error', 'something went wrong' );
        }
    }

    // STEP 1 FETCHING FIELD DATA USING API

    public function connectfb( Request $request ) {
        try {
            $adid = $request->adid;
            $accesstoken = $request->accesstoken;

            // First API request
            $url = "https://graph.facebook.com/v20.0/{$adid}/leads?fields=field_data&access_token={$accesstoken}";
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $response = curl_exec( $ch );
            curl_close( $ch );
            $leads = json_decode( $response, true );

            // Second API request for campaign master
            $url1 = "https://graph.facebook.com/v20.0/{$adid}?fields=name&access_token={$accesstoken}";
            curl_setopt( $ch, CURLOPT_URL, $url1 );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $response1 = curl_exec( $ch );
            curl_close( $ch );
            $campaign = json_decode( $response1, true );

            // Insert campaign details into campaign master
            $existingCampaign = DB::table( 'campaign_masters' )->where( 'ad_id', $adid )->first();

            if ( !$existingCampaign ) {
                $campaign_master = CampaignMaster::create( [
                    'ad_id' => $adid,
                    'access_token' => $accesstoken,
                    'campaign_name' => $campaign[ 'name' ],
                ] );
            } else {
                $campaign_master = $existingCampaign;
                // Use existing campaign
            }

            // Insert new fields into campaign fields
            if ( isset( $leads[ 'data' ] ) ) {
                $existingFields = DB::table( 'campaign_fields' )
                ->where( 'campaign_id', $campaign_master->id )
                ->pluck( 'field_name' )
                ->toArray();

                $newFields = [];

                foreach ( $leads[ 'data' ] as $submission ) {
                    foreach ( $submission[ 'field_data' ] as $field ) {
                        if ( !in_array( $field[ 'name' ], $existingFields ) && !in_array( $field[ 'name' ], $newFields ) ) {
                            $newFields[] = $field[ 'name' ];
                        }
                    }
                }

                if ( !empty( $newFields ) ) {
                    $insertData = array_map( function ( $fieldName ) use ( $campaign_master ) {
                        return [
                            'campaign_id' => $campaign_master->id,
                            'field_name' => $fieldName,
                        ];
                    }
                    , $newFields );

                    DB::table( 'campaign_fields' )->insert( $insertData );
                }

                $camid = $campaign_master->id;
            }

            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.fb-field-mapping', compact( 'camid' ) )->render(),
                'cam_id' => $campaign_master->id
            ] );
        } catch ( \Throwable $th ) {
            Log::error( $th );
            return redirect()->back()->with( 'error', 'Something went wrong' );
        }
    }

    // MULTIPLE CAMPAIGNS

    public function multipleCampaigns( Request $request ) {
        try {
            $adid = $request->adid;
            $accesstoken = $request->accesstoken;

            // First API request
            $url = "https://graph.facebook.com/v20.0/act_{$adid}?fields=ads{name}&access_token={$accesstoken}";
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $response = curl_exec( $ch );
            curl_close( $ch );
            $campaigns = json_decode( $response, true );

            foreach ( $campaigns[ 'ads' ][ 'data' ] as $campaign ) {
                $campaign_id = $campaign[ 'id' ];
                $campaign_name = $campaign[ 'name' ];

                $existingCampaign = CampaignMaster::where( 'camp_id', $campaign_id )->first();

                // dd( $existingCampaign );

                if ( !$existingCampaign ) {
                    CampaignMaster::create( [
                        'ad_id' => $adid,
                        'camp_id'=>$campaign_id,
                        'access_token' => $accesstoken,
                        'campaign_name' => $campaign_name,
                    ] );
                } else {
                    $existingCampaign->update( [
                        'ad_id' => $adid,
                        'camp_id'=>$campaign_id,
                        'access_token' => $accesstoken,
                        'campaign_name' => $campaign_name,
                    ] );
                }
            }

            // Insert campaign details into campaign master

            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.multiple-campaigns', compact( 'adid' ) )->render(),
                'ad_id' => $adid,
            ] );

        } catch ( \Throwable $th ) {
            Log::error( $th );
            return redirect()->back()->with( 'error', 'Something went wrong' );
        }
    }

    public function chooseCampaigns( Request $request ) {
        $selected_campaigns = $request->selectedCampaigns;

        // dd( $selected_campaigns );

        foreach ( $selected_campaigns as $camps ) {
            $campaign = CampaignMaster::where( 'id', $camps )->first();

            $campaign->update([
                'is_integrated'=>1
            ]);

            $campaignid = $campaign->camp_id;
            $access = $campaign->access_token;

            $url = "https://graph.facebook.com/v20.0/{$campaignid}/leads?fields=field_data&access_token={$access}";
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $response = curl_exec( $ch );
            curl_close( $ch );
            $leads = json_decode( $response, true );

            if ( isset( $leads[ 'data' ] ) ) {
                $existingFields = DB::table( 'campaign_fields' )
                ->where( 'campaign_id', $camps )
                ->pluck( 'field_name' )
                ->toArray();

                $newFields = [];

                foreach ( $leads[ 'data' ] as $submission ) {
                    foreach ( $submission[ 'field_data' ] as $field ) {
                        if ( !in_array( $field[ 'name' ], $existingFields ) && !in_array( $field[ 'name' ], $newFields ) ) {
                            $newFields[] = $field[ 'name' ];
                        }
                    }
                }

                if ( !empty( $newFields ) ) {
                    $insertData = array_map( function ( $fieldName ) use ( $camps ) {
                        return [
                            'campaign_id' => $camps,
                            'field_name' => $fieldName,
                        ];
                    }
                    , $newFields );

                    DB::table( 'campaign_fields' )->insert( $insertData );
                }

                $camid = $selected_campaigns;
            }

            // dd( $camps );
        }

        return response()->json( [
            'view' => view( 'pages.settings.facebook-integration.fb-field-mapping', compact( 'camid' ) )->render(),
            // 'cam_id' => $camps,
        ] );
    }

    public function mapfields( Request $request ) {
        try {
            $adid = $request->adid;
            $access_token = $request->accesstoken;
            $fieldMappings = $request->fieldMappings;
            $cam_id = $request->cam_id;
            $cams = $request->cams;

            foreach ( $cams as $cam ){
                foreach ( $fieldMappings as $fm ) {
                    $campaign_field_name = CampaignFields::where( 'id', $fm[ 'campaignFieldId' ] )->first();
                    $crm_field_name = DB::table( 'lead_form_fields' )->where( 'id', $fm[ 'crmFieldId' ] )->first();


                    CampaignFieldMigration::create( [
                        'campaign_id'=>$campaign_field_name->campaign_id,
                        'campaign_field_id'=>$fm[ 'campaignFieldId' ],
                        'lead_field_id'=>$fm[ 'crmFieldId' ],
                        'campaign_field_name'=>$campaign_field_name->label,
                        'crm_field_name'=>$crm_field_name->label,
                    ] );
                }

                $campaign = CampaignMaster::where('id', $cam)->first();
                if ($campaign) {
                    $campaign->new_fields = 0;
                    $campaign->save();
                }
            }

            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.lead-mapping', compact( 'cams' ) )->render(),
                'cam_id' => $cam_id
            ] );
        } catch ( \Throwable $th ) {
            Log::error( $th );
            return redirect()->back()->with( 'error', 'Something went wrong' );
        }

    }

    public function fbassignleads( Request $request ) {
        try {

            // $selectedUserIds = $request->assigned;
            // $camid = $request->camid;
            $cams = $request->cams;

            foreach ( $cams as $cam ) {
                $campaignId = $cam[ 'campaignId' ];
                $campaignAssigned = $cam[ 'assigned' ];

                $users = User::whereIn( 'id', $campaignAssigned )
                ->where( 'user_status', 'active' )
                ->get();

                $userCount = $users->count();

                if ( $userCount == 0 ) {
                    return response()->json( [ 'error' => 'No active selected users found' ], 404 );
                }

                foreach ( $users as $user ) {
                    $assignedUser = AssignedUser::create( [
                        'user_id'=>$user->id,
                        'user_name'=>$user->name,
                        'campaign_id'=>$campaignId,
                    ] );
                }
            }

            // return response()->json( [ 'success' => 'Lead assigned successfully', 'lead' => $assignedUser ] );
            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.lead-maping-success', compact( 'users', 'cams' ) )->render(),
            ] );

        } catch ( \Exception $e ) {
            return response()->json( [ 'error' => 'Failed to assign lead: ' . $e->getMessage() ], 500 );
        }
    }

    public function viewAssigned() {
        try {

            $active_campaigns = CampaignMaster::where( 'status', 1 )->where('is_integrated',1)->get();
            $assigned_users = AssignedUser::all();
            $users = User::where( 'user_status', 'active' )->get();

            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.assigned-user-view', compact( 'active_campaigns', 'assigned_users', 'users' ) )->with( 'success', 'page loaded successfully' )->render(),
            ] );
        } catch ( \Throwable $th ) {
            Log::error( $th );
            return redirect()->back()->with( 'error', 'Something went wrong' );
        }
    }

    public function assignUsers( Request $request ) {
        try {
            $selected = $request->selectedData;

            foreach ( $selected as $select ) {
                $campaign_id = $select[ 'campaignId' ];
                $users = $select[ 'users' ];

                $original_users = User::whereIn( 'id', $users )->get();

                foreach ( $original_users as $original_user ) {
                    AssignedUser::updateOrCreate(
                        [ 'campaign_id' => $campaign_id, 'user_id' => $original_user->id ],
                        [ 'user_name' => $original_user->name ]
                    );
                }
            }

            $active_campaigns = CampaignMaster::where( 'status', 1 )->where('is_integrated',1)->get();
            $assigned_users = AssignedUser::all();
            $users = User::where( 'user_status', 'active' )->get();

            return response()->json( [
                'view' => view( 'pages.settings.facebook-integration.assigned-user-view', compact( 'active_campaigns', 'assigned_users', 'users' ) )
                ->with( 'success', 'Users assigned successfully' )
                ->render(),
            ] );
        } catch ( \Throwable $th ) {
            Log::error( 'Error in assigning users: ' . $th->getMessage(), [ 'error' => $th ] );

            return redirect()->back()->with( 'error', 'Something went wrong while assigning users.' );
        }
    }

      /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function socialiteRedirect() {

        return Socialite::driver( 'facebook' )->scopes(['email', 'ads_management', 'ads_read','public_profile','business_management','pages_show_list','read_insights'])->redirect();
        // return Socialite::driver('facebook')
        // ->scopes(['ads_read', 'ads_management', 'business_management', 'pages_show_list', 'read_insights'])
        // ->redirect();
    }

    public function socialiteCallback(Request $request) {

        try {

            $user = Socialite::driver( 'facebook' )->user();


            $fb_user = FbuserMaster::create( [
                'fbuser_id'=>$user->id,
                'name'=>$user->name,
                'phone'=>$user->phone,
            ] );

            $shortLivedToken = $user->token;

            $appId = env('FACEBOOK_CLIENT_ID');
            $appSecret = env('FACEBOOK_CLIENT_SECRET');


            $url = "https://graph.facebook.com/v17.0/oauth/access_token?grant_type=fb_exchange_token&client_id=$appId&client_secret=$appSecret&fb_exchange_token=$shortLivedToken";

            // Initialize a cURL session
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $responseData = json_decode($response, true);

            // Check for any error
            if (isset($responseData['error'])) {
                return response()->json(['error' => $responseData['error']['message']], 400);
            }

            $accessToken = $responseData['access_token'];
            // $accessToken = $shortLivedToken;


            // First API request
            $url = "https://graph.facebook.com/v17.0/me?fields=id,name,adaccounts{name}&access_token={$accessToken}";
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $response = curl_exec( $ch );
            curl_close( $ch );
            $userData = json_decode( $response, true );

            if (isset($userData['error'])) {
                return redirect()->route('settings.fb_ac_error')->with('error', 'No ad account');
            }

            if($userData){

                $adaccounts = $userData['adaccounts'];

                $ads = $adaccounts['data'];

                foreach($ads as $ad){
                    $act_id = $ad['id'];
                    $act_name = $ad['name'];

                    $existingAdaccount = DB::table('ad_accounts')->where('act_id',$act_id)->first();


                    if ( !$existingAdaccount ) {
                        DB::table('ad_accounts')->insert([
                            'act_id' => $act_id,
                            'act_name'=>$act_name,
                            'token'=>$accessToken,
                        ]);
                    } else {
                        DB::table('ad_accounts')->where('act_id',$act_id)->update([
                            'act_id' => $act_id,
                            'act_name'=>$act_name,
                            'token'=>$accessToken,
                        ]);
                    }

                }

            }
                // dd($adaccounts['data']);

                $adid = $adaccounts['data'];
                $serializedAdId = urlencode(serialize($adid));

                return redirect()->route( 'settings.fb_multiple_accounts',['adid' => $serializedAdId])->with( 'success', 'Everything went as planned' );


        } catch ( \Exception $e ) {
            Log::error( 'Error logging user in facebook: ' . $e->getMessage(), [ 'error' => $e ] );

            // return redirect()->route( 'facebook_integration' )->with( 'error', 'Something went wrong while assigning users.' );
            return redirect()->back()->with('error','something went wrong');
        }
    }

       public function chooseadaccs(Request $request){
        $adaccounts = $request->selectedAdAccounts;

        foreach($adaccounts as $acc){

            $adaccount = DB::table('ad_accounts')->where('act_id',$acc)->first();
            $token = $adaccount->token;

            $url = "https://graph.facebook.com/v20.0/$acc/ads?fields=name&access_token={$token}&pretty=0&limit=500";

            // Exponential backoff parameters
            $retryCount = 0;
            $maxRetries = 5;
            $waitTime = 1; // Start with 1 second

            while ($retryCount < $maxRetries) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);

                $campaigns = json_decode($response, true);

                // Check for rate limit error
                if (isset($campaigns['error']) && $campaigns['error']['code'] == 17) {
                    // Rate limit reached, wait and retry
                    sleep($waitTime);
                    $waitTime *= 2; // Double the wait time for exponential backoff
                    $retryCount++;
                } else {
                    // No rate limit error, continue processing
                    if (isset($campaigns['data'])) {
                        $camps = $campaigns['data'];

                        foreach ($camps as $campaign) {
                            $campaign_id = $campaign['id'];
                            $campaign_name = $campaign['name'];

                            $existingCampaign = CampaignMaster::where('camp_id', $campaign_id)->first();

                            if (!$existingCampaign) {
                                CampaignMaster::create([
                                    'ad_id' => $acc,
                                    'camp_id' => $campaign_id,
                                    'access_token' => $token,
                                    'campaign_name' => $campaign_name,
                                ]);
                            } else {
                                $existingCampaign->update([
                                    'ad_id' => $acc,
                                    'camp_id' => $campaign_id,
                                    'access_token' => $token,
                                    'campaign_name' => $campaign_name,
                                ]);
                            }
                        }
                    }
                    break; // Exit retry loop if successful
                }
            }
        }

        $adid = $adaccounts;

        return response()->json([
            'view' => view('pages.settings.facebook-integration.multiple-campaigns', compact('adid'))->render(),
            'ad_id' => $adid,
        ]);
    }


    public function deleteintegration( Request $request ) {
        try {
            $camp_id = $request->camp_id;

            CampaignMaster::where( 'id', $camp_id )->update( [
                'is_integrated'=>0
            ] );

            CampaignFieldMigration::where( 'campaign_id', $camp_id )->delete();
            CampaignFields::where( 'campaign_id', $camp_id )->delete();
            AssignedUser::where('campaign_id',$camp_id)->delete();

            return response()->json(['success' => 'facebook integration deleted Successfully'], 200);

        } catch ( \Exception $e ) {
            Log::error( 'Error deleting facebook integration: ' . $e->getMessage(), [ 'error' => $e ] );
            return redirect()->back()->with( 'error', 'something went wrong' );
        }
    }

    public function editfieldmaps(Request $request)
    {
        try {
            $camp_id = $request->camid;

            // Get the campaign master record
            $campaign_master = CampaignMaster::find($camp_id);

            if (!$campaign_master) {
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            $campaignid = $campaign_master->camp_id;
            $access = $campaign_master->access_token;

            // Delete existing campaign fields and migration data
            CampaignFields::where('campaign_id', $camp_id)->delete();
            CampaignFieldMigration::where('campaign_id', $camp_id)->delete();
            AssignedUser::where('campaign_id',$camp_id)->delete();

            // Fetch leads data from Facebook Graph API
            $url = "https://graph.facebook.com/v20.0/{$campaignid}/leads?fields=field_data&access_token={$access}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $leads = json_decode($response, true);

            // Check if leads data is available
            if (isset($leads['data'])) {
                // Get existing fields for the campaign
                $existingFields = DB::table('campaign_fields')
                    ->where('campaign_id', $camp_id)
                    ->pluck('field_name')
                    ->toArray();

                $newFields = [];

                // Extract new fields from the leads data
                foreach ($leads['data'] as $submission) {
                    foreach ($submission['field_data'] as $field) {
                        if (!in_array($field['name'], $existingFields) && !in_array($field['name'], $newFields)) {
                            $newFields[] = $field['name'];
                        }
                    }
                }

                // Insert new fields into the campaign_fields table
                if (!empty($newFields)) {
                    $insertData = array_map(function ($fieldName) use ($camp_id) {
                        return [
                            'campaign_id' => $camp_id,
                            'field_name' => $fieldName,
                        ];
                    }, $newFields);

                    // Insert the new fields
                    DB::table('campaign_fields')->insert($insertData);
                }
            }

            // Fetch the updated campaign fields
            $campaign_fields = CampaignFields::where('campaign_id', $camp_id)->get();
            $camid = $camp_id;


            // Return the updated view
            return response()->json([
                'view' => view('pages.settings.facebook-integration.fb-edit-field-mapping', compact('camid'))->render(),
                'cam_id' => $camp_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error during Facebook integration update: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Something went wrong while updating the campaign fields.');
        }
    }


    public function fbMultipleCampaigns($adid){
        $adid = unserialize(urldecode($adid));
        return view('pages.settings.facebook-integration.fb-multiple-leads',compact('adid'))->with('success','Logged and fetched data successfully');
    }

    public function fbMultipleAdAccs($adid){
        $adid = unserialize(urldecode($adid));
        $ids = array_column($adid, 'id');
        $adAccs = DB::table('ad_accounts')->whereIn('act_id',$ids)->get();
        return view('pages.settings.facebook-integration.multiple-ad-accs',compact('adid','adAccs'))->with('success','Logged and fetched data successfully');
    }

    public function fberrorLogin(){
        return view('pages.settings.facebook-integration.fb-account-error')->with('error','ad account not found');
    }
}
