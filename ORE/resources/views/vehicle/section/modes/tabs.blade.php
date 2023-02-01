<ul class="nav nav-tabs finance_tabs">

@if($return_array['params_vechType'] == 'new' && $return_array['params_year'] >= 2019)
<li class="active rBold"><a data-toggle="tab" href="#lease" data-tab-name="lease" id="tab_lease" class="tab_payments">Lease</a></li>
 <li class=" rBold"><a data-toggle="tab" href="#finance" data-tab-name="finance"  id="tab_finance" class="tab_payments">Finance</a></li>
@else
	
 <li class="active rBold"><a data-toggle="tab" href="#finance" data-tab-name="finance"  id="tab_finance" class="tab_payments">Finance</a></li>
@endif

<li class="rBold"><a data-toggle="tab" href="#cash" data-tab-name="cash"  id="tab_cash" class="tab_payments">Cash</a></li>

</ul>   