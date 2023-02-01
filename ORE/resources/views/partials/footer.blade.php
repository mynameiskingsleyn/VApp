<footer class="rBold">
   <div class="row no-gutters text-center">
      <div class="col-xs-12">
         <div class="utilities">
         </div>
      </div>
      <div class="col-xs-12">
         <div class="fooBrandsList">
         </div>
      </div>
      <div class="col-xs-12">
      </div>
      <div style="background: black; width: 100%">
         <div class="container oreFooter">
            <div class="col-md-5 col-sm-5 text-left">
               <!-- <div class="footer-logos-field">
                  <a ><img alt="FCA Logo" title="FCA Logo" class="img-responsive" src="{{ cdn('images/footer-fca.png') }}"></a>
                  <a><img class="img-responsive" src="{{ cdn('images/rone_footer_bandw.png') }}" alt="Routeone Logo" title="Routeone Logo" width="56" height="25"></a>
				  <a><img alt="carzato logo" src="{{ cdn('images/carzato.jpg') }}"></a>
               </div> -->
               <ul class="footer-lists">
               @php 
                if(!isset($make)) $make = 'jeep';
               @endphp
               
               @if($make == 'chrysler')
                  <span class="footercontent" id="chrysler" >
                     <li>&copy; {{ date('Y') }} FCA US LLC. All Rights Reserved. Chrysler, Dodge, Jeep and Ram are registered trademarks of FCA US LLC. ALFA ROMEO and FIAT are registered trademarks of FCA Group Marketing S.p.A., used with permission.</li>

                     <li class="footer_margin_list">*MSRP excludes destination, taxes, title and registration fees. Starting at price refers to the base model, optional equipment not included. A more expensive model may be shown. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.</li>
                     <li class="footer_margin_list">
                        FCA strives to ensure that its website is accessible to individuals with disabilities. Should you encounter an issue accessing any content on Chrysler.com, please contact our Customer Service Team at 800-247-9753, for further assistance or to report a problem. Access to <a href="https://fcacommunity.force.com/Chrysler/s/" target="_blank">https://www.chrysler.com/webselfservice/chrysler</a> is subject to FCA’s Privacy Policy and Terms of Use.
                     </li>
                  </span>
                @elseif($make == 'dodge')  
                  <span class="footercontent" id="dodge" >
                     <li>&copy; {{ date('Y') }} FCA US LLC. All Rights Reserved. Chrysler, Dodge, Jeep and Ram are registered trademarks of FCA US LLC. ALFA ROMEO and FIAT are registered trademarks of FCA Group Marketing S.p.A., used with permission. </li>

                     <li class="footer_margin_list">*MSRP excludes destination, taxes, title and registration fees. Starting at price refers to the base model, optional equipment not included. A more expensive model may be shown. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer. </li>
                     <li class="footer_margin_list">
                        FCA strives to ensure that its website is accessible to individuals with disabilities. Should you encounter an issue accessing any content on Dodge.com, please contact our Customer Service Team at 800-4ADodge, for further assistance or to report a problem. Access to <a href="https://fcacommunity.force.com/Dodge/s/" target="_blank">https://www.dodge.com/webselfservice/dodge/index.html </a>is subject to FCA’s Privacy Policy and Terms of Use.
                     </li>
                  </span>
                 @elseif($make == 'jeep')     
                  <span class="footercontent" id="jeep" >
                     <li>&copy; {{ date('Y') }} FCA US LLC. All Rights Reserved. Chrysler, Dodge, Jeep and Ram are registered trademarks of FCA US LLC. ALFA ROMEO and FIAT are registered trademarks of FCA Group Marketing S.p.A., used with permission. </li>

                     <li class="footer_margin_list">*MSRP excludes destination, taxes, title and registration fees. Starting at price refers to the base model, optional equipment not included. A more expensive model may be shown. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.</li>
                     <li class="footer_margin_list">
                        FCA strives to ensure that its website is accessible to individuals with disabilities. Should you encounter an issue accessing any content on www.jeep.com, please <a href="https://www.jeep.com/webselfservice/jeep/EmailPage.html" target="_blank">email our Customer Service Team</a> or call 1-877-IAMJEEP, for further assistance or to report a problem. Access to <a href="https://fcacommunity.force.com/Jeep/s/" target="_blank"> https://www.jeep.com/webselfservice/jeep/index.html </a>is subject to FCA’s Privacy Policy and Terms of Use.
                     </li>
                  </span>
            @elseif($make == 'ram') 
                  <span class="footercontent" id="ram" >
                     <li>&copy; {{ date('Y') }} FCA US LLC. All Rights Reserved. Chrysler, Dodge, Jeep and Ram are registered trademarks of FCA US LLC. ALFA ROMEO and FIAT are registered trademarks of FCA Group Marketing S.p.A., used with permission. </li>

                     <li class="footer_margin_list">*MSRP excludes destination, taxes, title and registration fees. Starting at price refers to the base model, optional equipment not included. A more expensive model may be shown. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer. </li>
                     <li class="footer_margin_list">
                        FCA strives to ensure that its website is accessible to individuals with disabilities. Should you encounter an issue accessing any content on www.ramtrucks.com, please <a href="https://www.ramtrucks.com/webselfservice/ram/emailpage.html" target="_blank">email our Customer Service Team</a> or call 1-866-RAM-INFO, for further assistance or to report a problem. Access to <a href="https://fcacommunity.force.com/RAM/s/" target="_blank">https://www.ramtrucks.com/webselfservice/ram</a> is subject to FCA’s Privacy Policy and Terms of Use.
                     </li>
                  </span>
             @elseif($make == 'fiat')       
                <span class="footercontent" id="fiat" >
                     <li>&copy; {{ date('Y') }} FCA US LLC. All Rights Reserved. Chrysler, Dodge, Jeep and Ram are registered trademarks of FCA US LLC. ALFA ROMEO and FIAT are registered trademarks of FCA Group Marketing S.p.A., used with permission.</li>

                     <li class="footer_margin_list">*MSRP excludes destination, taxes, title and registration fees. Starting at price refers to the base model, optional equipment not included. A more expensive model may be shown. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer. </li>
                     <li class="footer_margin_list">
                     FCA strives to ensure that its website is accessible to individuals with disabilities. Should you encounter an issue accessing any content on fiatusa.com, please contact our Customer Service Team at <a href="https://fcacommunity.force.com/FIAT/s/"  target="_blank"> www.fiatusa.com/webselfservice/fiat</a> or 888-242-6342, for further assistance or to report a problem. Access to <a href="https://www.fiatusa.com/"  target="_blank"> fiatusa.com <a> is subject to FCA’s Privacy Policy and Terms of Use.
                     </li>
                  </span> 
              @else
 <span class="footercontent" id="chrysler" >
                     <li>&copy; {{ date('Y') }} FCA US LLC. All Rights Reserved. Chrysler, Dodge, Jeep and Ram are registered trademarks of FCA US LLC. ALFA ROMEO and FIAT are registered trademarks of FCA Group Marketing S.p.A., used with permission.</li>

                     <li class="footer_margin_list">*MSRP excludes destination, taxes, title and registration fees. Starting at price refers to the base model, optional equipment not included. A more expensive model may be shown. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.</li>
                     <li class="footer_margin_list">
                        FCA strives to ensure that its website is accessible to individuals with disabilities. Should you encounter an issue accessing any content on Chrysler.com, please contact our Customer Service Team at 800-247-9753, for further assistance or to report a problem. Access to <a href="https://fcacommunity.force.com/Chrysler/s/" target="_blank">https://www.chrysler.com/webselfservice/chrysler</a> is subject to FCA’s Privacy Policy and Terms of Use.
                     </li>
                  </span>
              @endif    
               </ul>
            </div>
            <div class="col-md-7 col-sm-7 footerLinks" >
               <ul>
                  <li><a href="https://www.chrysler.com/crossbrand_us/privacy" target="_blank" title="Privacy Policy" alt="Privacy Policy">Privacy Policy </a></li>
                  <!-- <li><a href="https://www.routeone.com/" target="_blank" title="Privacy Policy" alt="Privacy Policy">RouteOne</a></li> -->
                  <li><a href="https://www.chrysler.com/universal/Copyright.html" target="_blank" title="Copyright" alt="Copyright">Copyright </a></li>
                  <li><a href="https://www.chrysler.com/crossbrand/terms-of-use" target="_blank" title="Terms of Use" alt="Terms of Use">Terms of Use </a></li>
                  <li><a href="https://www.fcausdriveability.com/legal-safety-trademark/" target="_blank" title="Legal, Safety and Trademark Information" alt="Legal, Safety and Trademark Information">Legal, Safety and Trademark Information </a></li>
                  @if($make == 'chrysler')
<li><a href="https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility" id="Accessibility" target="_blank" title="Accessibility" alt="Accessibility">Accessibility</a></li>
                   @elseif($make == 'dodge') 
<li><a href="https://fcacommunity.force.com/Dodge/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility" id="Accessibility" target="_blank" title="Accessibility" alt="Accessibility">Accessibility</a></li>
                    @elseif($make == 'jeep') 
<li><a href="https://fcacommunity.force.com/Jeep/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility" id="Accessibility" target="_blank" title="Accessibility" alt="Accessibility">Accessibility</a></li>
                   @elseif($make == 'ram') 
<li><a href="https://fcacommunity.force.com/RAM/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility" id="Accessibility" target="_blank" title="Accessibility" alt="Accessibility">Accessibility</a></li>
                  @elseif($make == 'fiat') 
                  <li><a href="https://fcacommunity.force.com/FIAT/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility" id="Accessibility" target="_blank" title="Accessibility" alt="Accessibility">Accessibility</a></li>
                  @else
<li><a href="https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility" id="Accessibility" target="_blank" title="Accessibility" alt="Accessibility">Accessibility</a></li>
                  @endif
               </ul>
            </div>
         </div>
      </div>
   </div>
   <script>
    //   $(document).ready(function() {
    //      var anchor = "https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //      $('.footercontent').hide(); 
    //      $('.brandFrame').on('click', function() {
    //         var footerbrand_val = $(this).attr('data-make');
    //         localStorage.setItem("oremake", footerbrand_val);
    //         changefooter();
    //      });

    //      function changefooter() {
    //         var footerbrand = localStorage.getItem("oremake");
    //         switch (footerbrand) {
    //            case 'chrysler':
    //               $('.footercontent').hide();
    //               $('#chrysler, .footerLinks').show();
    //               anchor = "https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //               break;
    //            case 'dodge':
    //               $('.footercontent').hide();
    //               $('#dodge, .footerLinks').show();
    //               anchor = "https://fcacommunity.force.com/Dodge/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //               break;
    //            case 'jeep':
    //               $('.footercontent').hide();
    //               $('#jeep, .footerLinks').show();
    //               anchor = "https://fcacommunity.force.com/Jeep/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //               break;
    //            case 'ram':
    //               $('.footercontent').hide();
    //               $('#ram, .footerLinks').show();
    //               anchor = "https://fcacommunity.force.com/RAM/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //               break;
    //            case 'fiat':
    //               $('.footercontent').hide();
    //               $('#fiat, .footerLinks').show();
    //               anchor = "https://fcacommunity.force.com/FIAT/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //               break;
    //            default:
    //               $('.footercontent').hide();
    //               $('#chrysler, .footerLinks').show();
    //               anchor = "https://fcacommunity.force.com/Chrysler/s/article/What-steps-does-FCA-US-take-to-ensure-website-Accessibility";
    //         }
    //         $('#Accessibility').attr('href', anchor);
    //      }

    //      setTimeout(function () {
    //          changefooter();
    //      }, 5000);
    //   });
   </script>
</footer>