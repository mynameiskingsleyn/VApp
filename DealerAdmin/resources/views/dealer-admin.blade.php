@extends('layouts.app')
@if($tabname == 'dealerdiscounts')
@section('pageTitle' , ' - Discounts')
@else 
@section('pageTitle' , ' - Automated Discounts')
@endif
@section('content')
<div class="container bodyContainerWrapper">
    <div class="row dealerHead">
        <div class="col-md-11 rift-soft">
            <div class="rowOne"><span class="oreBox">ORE</span><span class="dealerTool">Dealer Admin Tool</span></div>
        </div>
        <div class="col-md-1">
            <div class="fcaLogo"><img class="fcaLogo" src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/fca.svg"></div>
        </div>
    </div>
    <div class="row result-buttons-row rift-soft">
        <div class="col-md-12">
            <div class="search-result-buttons">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <a href="{{ env('APP_URL') }}inventory">
                      @if($tabname == 'dealerdiscounts')
                      <label class="btn gcss-colors-element-primary gcss-colors-text-body-primary form-check-label active" 
                      style="border-radius: 15px 0px 0px 15px;">
                         @else
                      <label class="btn tab-discount-label gcss-colors-element-subdued gcss-colors-text-body-primary form-check-label" style="border-radius: 15px 0px 0px 15px;">
                         @endif
                          <input class="form-check-input" type="radio" name="discount_options" id="discount_option1" autocomplete="off" value="1" checked style="visibility: hidden;">
                          Discounts
                      </label>
                    </a>

                    <a href="{{ env('APP_URL') }}automated-inventory">
                       @if($tabname == 'dealerautomateddiscounts')
                      <label class="btn gcss-colors-element-primary gcss-colors-text-body-primary form-check-label active"
                      style="border-radius: 0px 15px 15px 0px;">
                        @else 
                      <label class="btn tab-autodiscount-label gcss-colors-element-subdued gcss-colors-text-body-primary form-check-label" style="border-radius: 0px 15px 15px 0px;">
                        @endif
                          <input class="form-check-input" type="radio" name="discount_options" id="discount_option2" autocomplete="off" value="2" style="visibility: hidden;"> Automated Discounts
                      </label>
                      </a>
                </div>
            </div>
        </div>
    </div>
    @if($tabname == 'dealerdiscounts')
      <!-- dealer-discount container-->
      @include('discount.dealer-discounts')
    @else 
      <!-- dealer-automated-discount container-->
      @include('rulediscount.dealer-automated-discounts')
    @endif
</div>
    <!-- Discount success  Popup Modal -->
    @include('popup.maximum-discount-modal')
    <!-- Discount Delete  Popup Modal -->
    @include('popup.delete-discount-modal')
    <!-- Discount success  Popup Modal -->
    @include('popup.success-autodiscount-modal')
    <!-- Discount success  Popup Modal -->
    @include('popup.warningunsave-autodiscount-modal')
    <!-- Payment Calculator Popup Modal -->
    @include('popup.payment-calculator-modal')
  @if($tabname == 'dealerdiscounts')
     <!-- Add Discount Modal Field-->
    @include('popup.discount-modal')
    <!-- Add Bulk Discount Modal Field-->
    @include('popup.bulk-discount-modal')
    <!-- Delete vindiscount Popup Modal -->
    @include('popup.delete-vindiscount-modal')
    <!-- Delete Bulk Popup Modal -->
    @include('popup.bulk-delete-discount-modal')
  @endif
  @if($tabname == 'dealerautomateddiscounts')
    <!-- Discount Delete  Popup Modal -->
    @include('popup.warning-autodiscount-modal')
         <!-- Add Discount Modal Field-->
    @include('popup.rule-discount-modal')
    @include('popup.delete-autodiscount-modal')
  @endif
  @include('popup.warning-max-discount-modal')
  @include('popup.delete-uncheck-discount-modal')
  @include('popup.uncheckunsave-autodiscount-modal')
  @include('popup.delete-all-discount-modal')
  @include('popup.confirmation-discount-modal')
  @include('popup.confirmation-bulk-discount-modal')
  @include('popup.no-discount-duplicate-modal')
  @include('popup.warning-duplicate-discount-names-modal')
@endsection