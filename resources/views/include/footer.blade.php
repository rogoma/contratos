            </div>
        </div>
    </div>
</div>

{{-- dispara control de fechas para contratos --}}
@include('include.footers.contracts')

{{-- @if(Auth::user()->role_id == 6)
    @include('include.footers.minor_purchase')
@endif

@if(Auth::user()->role_id == 9)
    @include('include.footers.tenders')
@endif

@if(Auth::user()->role_id == 10)
    @include('include.footers.exceptions')
@endif

@if(Auth::user()->role_id == 7)
    @include('include.footers.awards')
@endif

@if(Auth::user()->role_id == 8)
    @include('include.footers.contracts')
@endif --}}
