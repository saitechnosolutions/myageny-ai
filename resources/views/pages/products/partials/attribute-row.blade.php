{{--
  Single dynamic attribute row.
  Variables:
    $rowIndex   – integer index for name[] arrays
    $selectedId – pre-selected attribute_id (null for new rows)
    $value      – pre-filled value string
    $attribute  – Attribute model (for display name on edit) or null
--}}
<div class="pm-attr-row" data-row="{{ $rowIndex }}">
    <div class="pm-attr-row__drag">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9"  cy="5"  r="1" fill="currentColor"/>
            <circle cx="15" cy="5"  r="1" fill="currentColor"/>
            <circle cx="9"  cy="12" r="1" fill="currentColor"/>
            <circle cx="15" cy="12" r="1" fill="currentColor"/>
            <circle cx="9"  cy="19" r="1" fill="currentColor"/>
            <circle cx="15" cy="19" r="1" fill="currentColor"/>
        </svg>
    </div>

    <div class="pm-attr-row__select">
        <select name="attributes[{{ $rowIndex }}][attribute_id]"
                class="pm-select pm-attr-select"
                data-row="{{ $rowIndex }}"
                required>
            <option value="">— Select attribute —</option>
            {{-- Options injected by JS (product.js) when category loads --}}
            @if($attribute)
                <option value="{{ $attribute->id }}" selected>{{ $attribute->name }}</option>
            @endif
        </select>
        <input type="hidden" name="attributes[{{ $rowIndex }}][attribute_id]"
               class="pm-attr-id-hidden" value="{{ $selectedId ?? '' }}">
    </div>

    <div class="pm-attr-row__value">
        <input type="text"
               name="attributes[{{ $rowIndex }}][value]"
               class="pm-input pm-attr-value-input"
               value="{{ $value ?? '' }}"
               placeholder="Enter value…"
               list="preset-{{ $rowIndex }}"
               autocomplete="off">
        <datalist id="preset-{{ $rowIndex }}">
            {{-- Preset options injected by JS --}}
        </datalist>
    </div>

    <div class="pm-attr-row__unit pm-attr-unit-display" id="unit-{{ $rowIndex }}">
        {{-- Unit label injected by JS --}}
    </div>

    <button type="button" class="pm-icon-btn pm-icon-btn--delete pm-attr-remove" data-row="{{ $rowIndex }}" title="Remove">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>
</div>
