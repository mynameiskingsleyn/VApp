    <div class="col-md-1 pleaseSelect"><img class="arrowLeft" src="https://d1jougtdqdwy1v.cloudfront.net/dealeradmin/images/arrow-left.svg"></div>
    <div class="col-md-6 pleaseSelect">
        <div class="dashboardInstuct">
            <span class="moveRight">PLEASE SELECT A </span><span class="meBlue">MAKE</span><span>,</span>
            <br><span class="meBlue">MODEL YEAR</span>&nbsp;<span class="riftLight">AND</span>&nbsp;<span class="meBlue">VEHICLE</span><span>.</span></div>
    </div>
    <div class="col-md-9 gridTableWrapperLoader" style="background-color:transparent;display: none;"></div>
    <div class="col-md-9 gridTableWrapper" style="display: none;">
        <div class="search-result-field">
            <button class="btn rift-soft gcss-button add-bulk-discounts-button" id="add-bulk-discounts-button" onclick="getBulkDiscountVehicles();" data-toggle="modal" data-target="#add-bulk-discount-model">add bulk discounts <img src="img/svg/add-white.svg"></button>
            <table class="table table-hover table-striped" id="gridTableContainer">
                <thead class="rift-soft">
                    <tr align="center">
                        <th scope="col">VIN NUMBER</th>
                        <th scope="col">MAKE</th>
                         <th scope="col">MODEL</th>
                        <th scope="col">MODEL YEAR</th>
                        <th scope="col">TRIM</th>
                        <th scope="col">MSRP</th>
                        <th scope="col">DISCOUNTS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" align="center">Loading ...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>