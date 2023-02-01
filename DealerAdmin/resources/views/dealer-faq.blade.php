@extends('layouts.app')
@section('pageTitle' , ' - Help & FAQ')
@section('content')
<style>
    .faq-answer-header {
        font-weight: bold;
    }
</style>
<div class="container bodyContainerWrapper">
    <div class="row help_faq_head">
        <div class="col-md-11 rift-soft">
            <div class="rowOne"><span class="oreBox" style="padding: 0 10px 0px 0;">ORE</span><span class="dealerTool">Help</span></div>
            <div class="tutorial-field col-md-9 pad0" style="padding: 0 25px 0 0px;">
                <label class="tutorial-label font-weight-bold">How can we help you?</label>
                <h3>Tutorials</h3>
                <div class="video-links">
                    <a href="#" id="video-trigger_initial1" class="how-itworks-link" href="javascript:void(0);">
                        <div class="video-link-item">
                        </div>
                        <span>Access Dealer Admin Tool</span>
                    </a>
                    <a href="#" id="video-trigger_initial2" class="how-itworks-link" href="javascript:void(0);">
                        <div class="video-link-item">
                        </div>
                        <span>Add/Edit Vehicle Level discounts</span>
                    </a>
                    <a href="#" id="video-trigger_initial3" class="how-itworks-link" href="javascript:void(0);">
                        <div class="video-link-item">
                        </div>
                        <span>Add Bulk Discounts</span>
                    </a>
                    <a href="#" id="video-trigger_initial4" class="how-itworks-link" href="javascript:void(0);">
                        <div class="video-link-item">
                        </div>
                        <span> Edit/Delete Bulk Discounts</span>
                    </a>
                    <a href="#" id="video-trigger_initial5" class="how-itworks-link" href="javascript:void(0);">
                        <div class="video-link-item">
                        </div>
                        <span>Setup Automated Discounts</span>
                    </a>
                </div>
                <h3>FAQ</h3>
                <div class="panel-group faq-accordions" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1"> What is ORE? <i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse in">
                            <div class="panel-body"> ORE stands for Online Retail Experience. It enables a prospective buyer to select from available inventory, explore incentives, evaluate trade-in value and monthly payments. The buyer can also complete the optional credit application and review available Service Protection Plans. ORE consolidates all this information and submits one lead to the dealership.</div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"> How do I enroll in the Dealer Admin Tool? <i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse2" class="panel-collapse collapse">
                            <div class="panel-body"> Please contact <u>info@chryslerdigital.com</u> and ask to enroll in the dealer discounts program.</div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse3"> How do I log in to the dealer admin tool?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse3" class="panel-collapse collapse">
                            <div class="panel-body"><span class="faq-answer-header">Below are the steps to log in to the Dealer Admin Tool:</span>
                                <ol>
                                    <li>Login to DealerCONNECT using your credentials</li>
                                    <li>Click on the &ldquo;Sales&rdquo; tab</li>
                                    <li>Scroll down and look for &ldquo;FCA Digital&rdquo; section and click the link labelled &ldquo;FCA Digital Dealer&rdquo;</li>
                                    <li>On the right side of the screen, find the &ldquo;Training & References&rdquo; widget</li>
                                    <li>Click on the &ldquo;Online Retail Experience (ORE) Dealer Admin tool&rdquo; link</li>
                                    <li>The Dealer Admin Tool will open in a new browser tab. </li>
                                </ol>
                                <span class="faq-answer-header"> Please Note:</span>
                                <ol>
                                    <li> Clicking on &ldquo;LOGOUT&rdquo; will log you out from the Dealer Admin Tool and close the browser tab.
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">I am seeing a message &ldquo;Sorry! You are not enrolled for this program&rdquo; <i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse4" class="panel-collapse collapse">
                            <div class="panel-body">
                                <span class="faq-answer-header">In this case, one or more of the following could apply:</span>
                                <ol type="A">
                                    <li>Your dealership may not be currently enrolled in the program</li>
                                    <li>This program does not support your specific brand.
                                        (For more information contact: <u>info@chryslerdigital.com</u>)
                                    </li>
                                    <li>In rare cases, there may be an issue with your DealerCONNECT credentials </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">
                                    What can I do with the Dealer Admin Portal?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse5" class="panel-collapse collapse">
                            <div class="panel-body">
                                <span class="faq-answer-header"> As a dealer, you can:</span>
                                <ol>
                                    <li>Create and manage discounts at individual vehicle level or across multiple vehicles</li>
                                    <li>Activate/Deactivate a specific VIN</li>
                                    <li>Create and manage rules at Make/Model Year/Vehicle/Trim levels and extend these rules to new inventory.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse6"> What are the different types of discounts available?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse6" class="panel-collapse collapse">
                            <div class="panel-body"> <span class="faq-answer-header">There are three types of discounts available.</span> They are:
                                <ol>
                                    <li> Individual vehicle discounts: Discounts that can be created and applied to one specific VIN. These are subject to a maximum of five (5) discounts per vehicle or a maximum dollar value of $5,000.</li>
                                    <li> Bulk Discounts: Discounts that can be created and applied to multiple vehicles at one time. </li>
                                    <li> Automated or Rule-based discounts: Rules can be created at various levels (Make, Make-Model Year, Make-Model Year-Vehicle, Make-Model Year-Vehicle-Trim/s combinations) and will be applicable to new VINs that are added to the dealer inventory.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse7"> How can I add discounts to an individual vehicle?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse7" class="panel-collapse collapse">
                            <div class="panel-body"> The Discounts feature enables you to create and apply discounts at individual vehicle level. Follow the below steps:
                                <ol>
                                    <li>Search for vehicles using VIN or other search filter options on the left panel. Mandatory fields are marked with an asterisk (*)</li>
                                    <li>Once complete with filter options, click the &ldquo; Go &ldquo; button to view the list of vehicle/s matching the search criteria</li>
                                    <li>Click the &ldquo; Add &ldquo; button for each vehicle you wish to apply the discount. Then enter the discount details such as Discount Name, Dollar Amount or Percentage (%) Value, start and expiration dates.</li>
                                    <li>When complete, click the &ldquo; Save & Apply &rdquo; button to apply the discount.</li>
                                </ol>
                                <span class="faq-answer-header"> Please Note:</span>
                                <ul>
                                    <li>&#9679; There is a maximum of five (5) discounts that can be created/saved per vehicle. </li>
                                    <li> &#9679; If the discount dollar amount exceeds $5,000, an error message will display. You will need to adjust the discounts so &nbsp; the value of all discounts will be within $5,000, then click the &ldquo; Save & Apply &rdquo;button again. </li>
                                    <li>&#9679; To save this discount for future use, check the &ldquo; Add to Saved Discounts &rdquo;; check box.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse8">Can I edit the discount(s) added to a vehicle?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse8" class="panel-collapse collapse">
                            <div class="panel-body"> Yes. Follow the below steps to edit existing discount/s on a vehicle:
                                <ol>
                                    <li>Search for vehicles using the VIN number or search filter options on the left panel.</li>
                                    <li>Click the &ldquo;Go&rdquo; button to view the list of vehicle/s matching the search criteria</li>
                                    <li>Click the &ldquo;Edit&rdquo; button for each vehicle that requires editing.</li>
                                    <li>Edit the discount details as desired. </li>
                                    <li>When complete, click the &ldquo;Save & Apply&rdquo; button to apply your changes. </li>
                                </ol>
                                <span class="faq-answer-header"> Please Note:</span>
                                <ul>
                                    <li> &#9679; There is a maximum of five (5) discounts that can be created/saved per vehicle.</li>
                                    <li> &#9679; If the discount dollar amount exceeds $5,000, an error message will display. You will need to adjust the discounts so the value of all discounts will be within $5,000, then click the &ldquo;Save & Apply&rdquo; button again.<li></li>
                                    <li> &#9679; To save this discount for future use, check the &ldquo;Add to Saved Discounts&rdquo; check box.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse9"> Can I delete the discount(s) from a vehicle before the expiration date?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse9" class="panel-collapse collapse">
                            <div class="panel-body"> Yes, follow the below steps to delete existing discount on a vehicle:
                                <ol>
                                    <li>Search for vehicles using the VIN number or search filter options on the left panel.</li>
                                    <li>Click the &ldquo;Go&rdquo; button to view the list of vehicle/s matching the search criteria</li>
                                    <li>Click the &ldquo;Delete&rdquo; button for each discount that requires deletion.</li>
                                    <li>Click the &ldquo;Confirm Deletion&rdquo; button to apply all deletions. </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse10"> How can I add discounts to multiple vehicles together?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse10" class="panel-collapse collapse">
                            <div class="panel-body"> This can be accomplished using the bulk discounts feature. This enables you to create and apply discounts to more than one vehicle at a time. Follow the below steps:
                                <ol>
                                    <li>Search for vehicles by using the filter options on the left. Fields marked with an asterisk (*) are mandatory. </li>
                                    <li>Click the &ldquo;Go&ldquo; button to view the list of vehicles matching the search criteria</li>
                                    <li>Click the &ldquo;Add Bulk Discounts&rdquo; button</li>
                                    <li>Enter Discount Name, Dollar Amount or Percentage (%) value, start and expiration dates</li>
                                    <li>Check the &ldquo; Include &rdquo; check box for the vehicles you would like to include. To apply to all vehicles, check the &ldquo; Include &rdquo; check box in the header.</li>
                                    <li>Click the &ldquo; Save & Apply &rdquo; button to apply these discounts</li>
                                    <li>Confirm overriding of all previously applied discounts.</li>
                                    <li>The discount/s are now applied to the selected vehicles</li>
                                </ol>
                                <span class="faq-answer-header">Please Note:</span>
                                <ul>
                                    <li>&#9679; There is a maximum of five (5) discounts that can be created/saved per vehicle.<li>
                                    <li>&#9679; If the discount dollar amount exceeds $5,000, an error message will display. You will need to adjust the discounts so the value of all discounts will be within $5,000, then click the &ldquo;Save & Apply&rdquo; button again.</li>
                                    <li>&#9679; To save this discount for future use, check the &ldquo; Add to Saved Discounts &rdquo; check box.</li>
                                    <li>&#9679; If one or more discounts are defined in percentage (%) terms and the calculated amount exceeds $5,000 on any of the vehicles, the discount amount will automatically be adjusted to the maximum limit of $5,000 and a warning sign and message will display for each vehicle indicating the this issue.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse11"> How can I edit discounts for multiple vehicles at once?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse11" class="panel-collapse collapse">
                            <div class="panel-body">Bulk discounts feature allows you to edit the bulk discounts. Follow the below steps:
                                <ol>
                                    <li>Search for vehicles by using the filter options on the left. Fields marked with an asterisk (*) are mandatory. </li>
                                    <li>Click the &ldquo;Go&rdquo;; button to view the list of vehicles matching the search criteria</li>
                                    <li>Click the &ldquo;Add Bulk Discounts&rdquo;button</li>
                                    <li>Edit the discount details as desired or add/delete discount(s) as needed.</li>
                                    <li>Check the &ldquo;Include&rdquo; check box for the vehicles you would like to include. To apply to all vehicles, check the &ldquo; Include &rdquo; check box in the header.</li>
                                    <li>Click the &ldquo;Save & Apply&rdquo; button to apply these discounts</li>
                                    <li>Confirm overriding of all previously applied discounts.</li>
                                    <li>The discount/s are now applied to the selected vehicles</li>
                                </ol>
                                <span class="faq-answer-header"> Please Note:</span>
                                <ul>
                                    <li>
                                        &#9679; There is a maximum of five (5) discounts that can be created/saved per vehicle.</li>
                                    <li>&#9679; If the discount dollar amount exceeds $5,000, an error message will display. You will need to adjust the discounts so the value of all discounts will be within $5,000, then click the &ldquo;Save & Apply&rdquo; button again.</li>
                                    <li>&#9679; To save this discount for future use, check the &ldquo;Add to Saved Discounts &rdquo; check box.</li>
                                    <li>&#9679; If one or more discounts are defined in percentage (%) terms and the calculated amount exceeds $5,000 on any of the vehicles, the discount amount will automatically be adjusted to the maximum limit of $5,000 and a warning sign and message will display for each vehicle indicating the this issue.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse12"> How can I delete one or more discounts from multiple vehicles?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse12" class="panel-collapse collapse">
                            <div class="panel-body"> Delete discounts using bulk discount functionality allows you to remove discounts from multiple
                                <ol>
                                    <li>Search for vehicles by using the filter options on the left. Fields marked with an asterisk (*) are mandatory. </li>
                                    <li>Click the &ldquo;Go&rdquo; button to view the list of vehicles matching the search criteria</li>
                                    <li>Click the &ldquo;Add Bulk Discounts&rdquo; button</li>
                                    <li>Select the vehicles that you would like to clear the discounts</li>
                                    <li>Click the &ldquo;Clear All Discounts&rdquo; button</li>
                                    <li>Confirm clearing of all discounts from the selected vehicles</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse13"> What is an &ldquo;Automated discounts&rdquo; feature?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse13" class="panel-collapse collapse">
                            <div class="panel-body"> This feature allows you to create rules at various levels and even for future inventory. Follow the below steps:
                                <ol>
                                    <li>Select the level at which the rule must be defined. Selection of &ldquo;Make&rdquo; is mandatory.</li>
                                    <li>Click the &ldquo;Create Discounts&rdquo; button.</li>
                                    <li>Enter discount details such as Discount Name, Dollar Amount or Percentage (%) Value, start and expiration dates. </li>
                                    <li>Click the &ldquo;Show & Edit Inventory&rdquo; button to view a list of vehicles based on the search filter combination selected</li>
                                    <li>All vehicles will be selected by default. To exclude vehicles from this discount, you must uncheck the vehicle.</li>
                                    <li>Click the &ldquo;Save & Apply&rdquo;button. This will apply the rules defined and override existing discounts. </li>
                                </ol>
                                <span class="faq-answer-header"> Please Note:</span>
                                <ul>
                                    &#9679; There is a maximum of five (5) discounts that can be created/saved per vehicle.
                                    <li>
                                        &#9679; If one or more discounts are defined in percentage (%) terms and the calculated amount exceeds $5,000 on any of the vehicles, the discount amount will automatically be adjusted to the maximum limit of $5,000 and a warning sign and message will display for each vehicle indicating the this issue.</li>
                                    <li>
                                        &#9679; By default, the &ldquo;Add to Saved Discounts&rdquo; check box is checked. Saved discounts can be viewed by clicking the &ldquo;Saved Discounts&rdquo; button.</li>
                                    <li>
                                        &#9679; Discounts are inherited by all vehicles matching the search criteria. Any changes made at superseding levels will override the existing rules.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse14"> Can I edit the rules once defined?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse14" class="panel-collapse collapse">
                            <div class="panel-body"> Yes. The tool enables you to edit the existing rules at various levels and the changes will affect future inventory. Follow the below steps:
                                <ol>
                                    <li>Select the level at which the rule must be edited. </li>
                                    <li>Click the &ldquo;Create Discounts&rdquo; button.</li>
                                    <li>Existing rule(s) defined for the selected filter option will display</li>
                                    <li>Edit discount details such as Discount Name, Dollar Amount or Percentage (%) Value, start and expiration dates, as desired. </li>
                                    <li>Click the &ldquo;Show & Edit Inventory&rdquo; button to view a list of vehicles for the search filter combination selected</li>
                                    <li>All vehicles are selected by default. To exclude vehicles or edit discounts, you must uncheck them at a vehicle level.</li>
                                    <li>Click on Save and apply. This will apply the changes made and override existing discounts.</li>
                                </ol>
                                <span class="faq-answer-header"> Please Note:</span>
                                <ul>
                                    <li>
                                        &#9679; There is a maximum of five (5) discounts that can be created/saved per vehicle.</li>
                                    <li>
                                        &#9679; If one or more discounts are defined in percentage (%) terms and the calculated amount exceeds $5,000 on any of the vehicles, the discount amount will automatically be adjusted to the maximum limit of $5,000 and a warning sign and message will display for each vehicle indicating the this issue.</li>
                                    <li>
                                        &#9679; By default, the &ldquo;Add to Saved Discounts&rdquo; check box is checked. Saved discounts can be viewed by clicking the &ldquo;Saved Discount&rdquo; button.</li>
                                    <li>
                                        &#9679; Discounts are inherited by all vehicles matching the search criteria. Any changes made at superseding levels will override the existing rules.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse15"> How to delete automated discounts?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse15" class="panel-collapse collapse">
                            <div class="panel-body">Follow the below steps to delete the automated discounts:
                                <ol>
                                    <li>Select the level at which the discounts/rule must be deleted. </li>
                                    <li>Click the &ldquo;Create Discounts&rdquo; button.</li>
                                    <li>Existing rule(s) defined for the selected filter option will display</li>
                                    <li>Click the &ldquo;Trashcan&rdquo; icon for each discount and confirm deletion. You can delete all discounts from the rule. </li>
                                    <li>The discounts will be cleared from all the vehicles where they were applied</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse16"> What is the difference between bulk and automated discounts ?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse16" class="panel-collapse collapse">
                            <div class="panel-body"> Bulk discounts are automated discounts can be applied to multiple vehicles at a time. Key differences between the two have been noted below:
                                <br>
                                <ol>
                                    <li>The discounts are applied to only selected vehicles in bulk functionality. In Automated Discounts the rules defined at a certain level will be inherited by new VINs getting added to dealership inventory.</li>
                                    <li>In the bulk functionality, the discounts can be applied to selected vehicles of a specific combination of Make-Model-Year-Vehicle (one or more)-Trim combinations. In Automated Discounts, rules can be applied at a Make level which will get applied to all vehicles of that Make.</li>
                                    <li>Bulk Discounts can be applied only to existing inventory. Automated Discounts can help you create rules for your future Inventory. Once the new inventory gets added, Automated Discounts will be automatically applied.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse17">How to activate/deactivate a VIN? What is the impact of the same?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse17" class="panel-collapse collapse">
                            <div class="panel-body"> Activating or deactivating a VIN influences the vehicle being listed on the SNI (Search New Inventory) page of the ORE platform. Follow the below steps:
                                <ol>
                                    <li>Search for vehicles by using the filter options on the left. Fields marked with an asterisk (*) are mandatory. </li>
                                    <li>Click the &ldquo;Go&rdquo; button to view the list of vehicles matching the search criteria</li>
                                    <li>Identify the vehicle you want to deactivate</li>
                                    <li>Click the toggle button under the VIN to &ldquo;Deactivate&rdquo; that vehicle</li>
                                    <li>The change will be reflected on the next Business day.</li>
                                </ol>
                                <span class="faq-answer-header">Impact of deactivation:</span>
                                <ul>
                                    <li>
                                        &#9679; The deactivated VIN will not appear in the search results on ORE platform.</li>
                                    <li>
                                        &#9679; Discounts cannot be added/modified for a deactivated VIN</li>
                                    <li>
                                        &#9679; The vehicle will remain in deactivated state until the toggle is switched back to Active status.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title ">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse18"> What if I have additional questions or need assistance with set-up?<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </h4>
                        </div>
                        <div id="collapse18" class="panel-collapse collapse">
                            <div class="panel-body"> For further assistance:
                            Please send an e-mail to <u>dealeradminsupport@v2soft.com</u>, OR Call: 833-435-7676 between 8:00 AM to 04:00 PM ET.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 sub-form-field">
                <form id="feedbackform" name="feedbackform">
                    <h4>Feedback Form</h4>
                    <div class="form-group">
                        <input type="text" id="fedbackname" name="fedbackname">
                        <div class="placeholder" id="place_fedbackname">NAME<span>*</span></div>
                        <span id="fedbackname_error" class="fedbackname bulk-error"> </span>
                    </div>
                    <div class="form-group">
                        <input type="text" value="{{ \Session::get('DealerAdmin')['DealerCode']}}" id="feadbackdealership" name="dealership">
                        <div class="placeholder" id="dealershipplaceholder">DEALERSHIP NAME/ID<span>*</span></div>
                        <span id="feadback_error" class="feadbackdealership bulk-error"> </span>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" id="feadbackemail">
                        <div class="placeholder">EMAIL<span>*</span></div>
                        <span id="feadbackemail_error" class="feadbackemail bulk-error"> </span>
                    </div>
                    <div class="form-group">
                        <input type="tel" maxlength="14" id="feedbackphone" name="feedbackphone">
                        <div class="placeholder">PHONE NUMBER</div>
                        <span id="feedbackphone_error" class="validphone bulk-error"></span>
                    </div>
                    <div class="form-group form-group__message">
                        <label class="text-area">Message <span style="color: red">*</span></label>
                        <textarea id="fedbackmessage" name="message"></textarea>
                        <span id="fedbackmessage_error" class="fedbackmessage bulk-error"></span>
                    </div>
                    <button type="button" onclick="faqsubmit();" class="feedback-btn btn">send</button>
                    <!-- <input type="button" onclick="faqsubmit();" class="feedback-btn btn" value="SEND"> -->
                </form>
            </div>
        </div>
        <div class="col-md-1">
            <div class=""><img class="fcaLogo" src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/fca.svg"></div>
        </div>
    </div>
    <!-- dealer-discount container-->
</div>
<!-- <div id="vidBox" style="display: none;">
    <div id="videCont">
        <video id="howitswork" loop controls controlsList="nodownload">
            <source src="img/alfa.mp4" type="video/mp4">
        </video>
    </div>
</div> -->
<div id="vidBox_initial1" style="display: none;">
    <div id="videCont_initial1">
        <video id="howitswork_initial1" loop controls controlsList="nodownload">
            <source src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/videos/ORE_DealerAdmin_Login.mp4" type="video/mp4">
        </video>
    </div>
</div>
<div id="vidBox_initial2" style="display: none;">
    <div id="videCont_initial2">
        <video id="howitswork_initial2" loop controls controlsList="nodownload">
            <source src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/videos/ORE_DealerAdmin_IndividualDiscount.mp4 " type="video/mp4">
        </video>
    </div>
</div>
<div id="vidBox_initial3" style="display: none;">
    <div id="videCont_initial3">
        <video id="howitswork_initial3" loop controls controlsList="nodownload">
            <source src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/videos/ORE_DealerAdmin_BulkDiscount_Add.mp4" type="video/mp4">
        </video>
    </div>
</div>
<div id="vidBox_initial4" style="display: none;">
    <div id="videCont_initial4">
        <video id="howitswork_initial4" loop controls controlsList="nodownload">
            <source src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/videos/ORE-DealerAdmin_Bulk_EditDiscount.mp4" type="video/mp4">
        </video>
    </div>
</div>
<div id="vidBox_initial5" style="display: none;">
    <div id="videCont_initial5">
        <video id="howitswork_initial5" loop controls controlsList="nodownload">
            <source src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/videos/ORE_DealerAdmin_RuleBasedDiscount.mp4 " type="video/mp4">
        </video>
    </div>
</div>
@include('popup.success-feedback-modal')
@include('popup.error-feedback-modal')
@endsection
