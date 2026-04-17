@if(session('success'))
<div class="crm-alert crm-alert-success">
    <span>✅ {{ session('success') }}</span>
    <button onclick="this.parentElement.remove()">✕</button>
</div>
@endif
@if($errors->any())
<div class="crm-alert crm-alert-error">
    <span>⚠️ {{ $errors->first() }}</span>
    <button onclick="this.parentElement.remove()">✕</button>
</div>
@endif

<style>
.crm-alert {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 20px; border-radius: 10px; margin-bottom: 16px;
    font-size: 14px; font-weight: 500;
}
.crm-alert-success { background: #f1fdf6; border: 1px solid #c3edd8; color: #225247; }
.crm-alert-error   { background: #fff4f4; border: 1px solid #ffc9c9; color: #a00; }
.crm-alert button  { background: none; border: none; cursor: pointer; font-size: 16px; opacity: .6; }
</style>
