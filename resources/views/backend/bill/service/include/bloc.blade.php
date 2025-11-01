<tr>
    <td class="text-center">
        <input type="checkbox" name="services[]" id="check-row" value="{{ json_encode($service) }}" class="form-check-input">
    </td>
    <td>
        {{ $service['name'] }}
    </td>
    <td>
        {{ $service['id'] }}
    </td>
    <td>
        Nigeria
    </td>
    <td>
        {{ $service['meta']['fee'] ?? 0 }}
    </td>
    <td>

        <button type="button" @class([
            'd-none' => in_array($service['id'], $servicesIds),
            'round-icon-btn',
            'red-btn',
            'addServiceBtn',
        ]) id="addService" data-info="{{ json_encode($service) }}">
            <i data-lucide="plus-circle"></i>
        </button>
        <button type="button" @class([
            'd-none' => !in_array($service['id'], $servicesIds),
            'round-icon-btn primary-btn',
            'addServiceBtn',
        ])
            @if (in_array($service['id'], $servicesIds)) data-info="{{ json_encode($service) }}" @endif id="addedService">
            <i data-lucide="check-circle"></i>
        </button>
    </td>
</tr>
