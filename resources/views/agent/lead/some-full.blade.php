<table class="table table-bordered table-striped table-hover">
    <tr>
        <th>{!! trans("site/lead.data") !!}</th><td>{!! $lead->date !!}</td>
    </tr>
    <tr>
        <th>{!! trans("site/lead.name") !!}</th><td>{!! $lead->name !!}</td>
    </tr>
    <tr>
        <th>{!! trans("site/lead.phone") !!}</th><td>{!! $lead->phone->phone !!}</td>
    </tr>
    <tr>
        <th>{!! trans("site/lead.email") !!}</th><td>{!! $lead->email !!}</td>
    </tr>
    @forelse($lead->sphereAttributes as $attribute)
        <tr>
            <th>{!! $attribute->label !!}</th>
            <td>
                @forelse($attribute->options as $option)
                    {!! $option->value !!}
                @empty
                @endforelse
            </td>
        </tr>
    @empty
    @endforelse
</table>

