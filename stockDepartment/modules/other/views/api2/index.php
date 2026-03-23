<?php

?>
<h2>Current url: http://31.145.4.213/DFStore.ProxyServices/ExternalWMS/ExternalWMSProxy.asmx?WSDL</h2>
<table class="table table-striped- table-bordered">
<!--    <thead>-->
        <tr>
            <th width="20%">Method name</th>
            <th width="30%">Params</th>
            <th>Action</th>
        </tr>
<!--    </thead>-->
<!--    <tbody>-->
    <tr>
        <td colspan="3" class="bg-success"><h4 class="text-left">INBOUND</h4></td>
    </tr>
    <tr>
        <td>GetWarehouseAppointments</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="GetWarehouseAppointments">Call Method</span></td>
    </tr>
    <tr>
        <td>MarkAppointmentforInBound</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">AppointmentBarcode:</div>
            <div class="col-md-6" style="margin-bottom: 10px;">
                <input type="text" name="AppointmentBarcode" id="MarkAppointmentforInBound-AppointmentBarcode" />
            </div>
        </td>
        <td><span class="btn btn-success" id="MarkAppointmentforInBound">Call Method</span></td>
    </tr>
    <tr>
        <td>PrepareInboundData</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="PrepareInboundData">Call Method</span></td>
    </tr>
    <tr>
        <td>GetAppointmentInBoundData</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">AppointmentBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="AppointmentBarcode" id="GetAppointmentInBoundData-AppointmentBarcode" /></div>
        </td>
        <td><span class="btn btn-success" id="GetAppointmentInBoundData">Call Method</span></td>
    </tr>
    <tr>
        <td>SendInBoundFeedBackData</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">InBoundId:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="InBoundId" id="SendInBoundFeedBackData-InBoundId" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">AppointmentBarcode:</div>  <div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="AppointmentBarcode" id="SendInBoundFeedBackData-AppointmentBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">PackBarcode:</div>  <div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="PackBarcode" id="SendInBoundFeedBackData-PackBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuBarcode:</div>  <div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuBarcode" id="SendInBoundFeedBackData-SkuBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuQuantity:</div>  <div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuQuantity" id="SendInBoundFeedBackData-SkuQuantity" /></div>
        </td>
        <td><span class="btn btn-success" id="SendInBoundFeedBackData">Call Method</span></td>
    </tr>
    <tr>
        <td>MarkAppointmentforCompleted</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">AppointmentBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="AppointmentBarcode" id="MarkAppointmentforCompleted-AppointmentBarcode" /></div>
        </td>
        <td><span class="btn btn-success" id="MarkAppointmentforCompleted">Call Method</span></td>
    </tr>
    <tr>
        <td colspan="3" class="bg-success"><h4 class="text-left">OUTBOUND</h4></td>
    </tr>
    <tr>
        <td> GetBatchsWms (GetWarehousePickings OLD)</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="GetBatchsWms">Call Method</span></td>
    </tr>
    <tr>
        <td>
            MarkPickingforOutBound OLD
        </td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">PickingId:</div>
            <div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="PickingId" id="MarkPickingforOutBound-PickingId" /></div>
        </td>
        <td>
            <span class="btn btn-success" id="MarkPickingforOutBound">Call Method</span>
        </td>
    </tr>
    <tr>
        <td>PrepareOutboundData</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="PrepareOutboundData">Call Method</span></td>
    </tr>
    <tr>
        <td>GetOutBoundData (GetPickingOutBoundData OLD)</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">BatchId:</div>
            <div class="col-md-6" style="margin-bottom: 10px;">
                <input type="text" name="PickingId" id="GetOutBoundData-BatchId" />
            </div>
        </td>
        <td>
            <span class="btn btn-success" id="GetOutBoundData">Call Method</span>
        </td>
    </tr>
    <tr>
        <td>SendOutBoundFeedBackData</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">OutBoundId:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="OutBoundId" id="SendOutBoundFeedBackData-OutBoundId" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">InBoundId:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="InBoundId" id="SendOutBoundFeedBackData-InBoundId" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">PackBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="PackBarcode" id="SendOutBoundFeedBackData-PackBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuBarcode" id="SendOutBoundFeedBackData-SkuBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuQuantity:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuQuantity" id="SendOutBoundFeedBackData-SkuQuantity" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">WaybillSerial:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" value="KZK" name="WaybillSerial" id="SendOutBoundFeedBackData-WaybillSerial" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">WaybillNumber:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" value="" name="WaybillNumber" id="SendOutBoundFeedBackData-WaybillNumber" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">Volume:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" value="32" name="Volume" id="SendOutBoundFeedBackData-Volume" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">CargoShipmentNo:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" value="" name="CargoShipmentNo" id="SendOutBoundFeedBackData-CargoShipmentNo" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">InvoiceNumber:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" value="" name="InvoiceNumber" id="SendOutBoundFeedBackData-InvoiceNumber" /></div>
        </td>
        <td>
            <span class="btn btn-success" id="SendOutBoundFeedBackData">Call Method</span>
        </td>
    </tr>

    <tr>
        <td>MarkBatchforCompleted (MarkPickingforCompleted OLD)</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">BatchId:</div>
            <div class="col-md-6" style="margin-bottom: 10px;">
                <input type="text" name="PickingId" id="MarkBatchforCompleted-BatchId" /></div>
        </td>
        <td><span class="btn btn-success" id="MarkBatchforCompleted">Call Method</span></td>
    </tr>
    <tr><td colspan="3" class="bg-success"><h4 class="text-left">RETURN</h4></td></tr>
    <tr>
        <td>GetInBoundDataForReturn</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="GetInBoundDataForReturn">Call Method</span></td>
    </tr>
    <tr>
        <td>SendInBoundFeedBackDataForReturn</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">InboundId:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="ProcessRequestedDataType" id="SendInBoundFeedBackDataForReturn-InboundId" value="0" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">AppointmentBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="AppointmentBarcode" id="SendInBoundFeedBackDataForReturn-AppointmentBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">PackBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="PackBarcode" id="SendInBoundFeedBackDataForReturn-PackBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuBarcode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuBarcode" id="SendInBoundFeedBackDataForReturn-SkuBarcode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuQuantity:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuQuantity" id="SendInBoundFeedBackDataForReturn-SkuQuantity" /></div>
        </td>
        <td>
            <span class="btn btn-success" id="SendInBoundFeedBackDataForReturn">Call Method</span>
        </td>
    </tr>

    <tr><td colspan="3" class="bg-success"><h4 class="text-left">CONTENT WMS DATA</h4></td></tr>
    <tr>
        <td>GetBusinessUnitWMSData</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="GetBusinessUnitWMSData">Call Method</span></td>
    </tr>
    <tr>
        <td>GetMasterData</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">ProcessRequestedDataType:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="ProcessRequestedDataType" id="GetMasterData-ProcessRequestedDataType" value="Full" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">ShortCode:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="ShortCode" id="GetMasterData-ShortCode" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">SkuId:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="SkuId" id="GetMasterData-SkuId" /></div>
            <div class="col-md-6" style="margin-bottom: 10px;">Ean:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="Ean" id="GetMasterData-Ean" /></div>
        </td>
        <td>
            <span class="btn btn-success" id="GetMasterData">Call Method</span>
            <span class="btn btn-success" id="GetMasterDataLoad">Call Method Load</span>
        </td>
    </tr>
    <tr>
        <td>GetSKUContentWMSData</td>
        <td>&nbsp;</td>
        <td><span class="btn btn-success" id="GetSKUContentWMSData">Call Method</span></td>
    </tr>
    <tr>
        <td>CreateLcBarcode</td>
        <td>
            <div class="col-md-6" style="margin-bottom: 10px;">Count:</div><div class="col-md-6" style="margin-bottom: 10px;"><input type="text" name="Count" id="CreateLcBarcode-Count" value="0" /></div>
        </td>
        <td>
            <span class="btn btn-success" id="CreateLcBarcode">Call Method</span>
        </td>
    </tr>
<!--    </tbody>-->
</table>
<h1 id="result-head">Result:</h1>
<div id="result"></div>

<script type="text/javascript">
    // ONLY  FOR TEST
    var callMethodHandler = function(url,me,params) {

        console.log('CLICK ON '+me.attr('id'));
        me.html('Please wait');
        $.post("http://wms.nmdx.kz/other/api2/"+url,params,function(data) {
            me.html('Call Method');
            $('#result').html(data);
            window.location.href = "#result-head";
        });
    };

    $(function() {
        /* 1 INBOUND */
        $("#GetWarehouseAppointments").on('click',function() {
            var me = $(this);
            callMethodHandler('get-warehouse-appointments',me,{});
        });
        /* 2 INBOUND */
        $("#MarkAppointmentforInBound").on('click',function() {
            var me = $(this),
                params = {'AppointmentBarcode':$('#MarkAppointmentforInBound-AppointmentBarcode').val()};

            callMethodHandler('mark-appointmentfor-in-bound',me,params);
        });
        /* 3 INBOUND */
        $("#PrepareInboundData").on('click',function() {
            var me = $(this);
            callMethodHandler('prepare-inbound-data',me,{});
        });
        /* 4 INBOUND */
        $("#GetAppointmentInBoundData").on('click',function() {
            var me = $(this),
                params = {'AppointmentBarcode':$('#GetAppointmentInBoundData-AppointmentBarcode').val()};

            callMethodHandler('get-appointment-inbound-data',me,params);
        });
        /* 5 INBOUND */
        $("#SendInBoundFeedBackData").on('click',function() {
            var me = $(this),
                params = {
                    'InBoundId':$('#SendInBoundFeedBackData-InBoundId').val(),
                    'AppointmentBarcode':$('#SendInBoundFeedBackData-AppointmentBarcode').val(),
                    'PackBarcode':$('#SendInBoundFeedBackData-PackBarcode').val(),
                    'SkuBarcode':$('#SendInBoundFeedBackData-SkuBarcode').val(),
                    'SkuQuantity':$('#SendInBoundFeedBackData-SkuQuantity').val()
                };

            callMethodHandler('send-inbound-feedback-data',me,params);
        });
        /* 6 INBOUND */
        $("#MarkAppointmentforCompleted").on('click',function() {
            var me = $(this),
                params = {'AppointmentBarcode':$('#MarkAppointmentforCompleted-AppointmentBarcode').val()};

            callMethodHandler('mark-appointmentfor-completed',me,params);
        });



        /* 1 OUTBOUND */
        $("#GetBatchsWms").on('click',function() {
            console.log('CLICK ON #GetBatchsWms');
            var me = $(this);
            callMethodHandler('get-batchs-wms',me,{});
        });
        /* 2 OUTBOUND */
        $("#MarkPickingforOutBound").on('click',function() {
            console.log('CLICK ON #MarkPickingforOutBound');
            var me = $(this),
                params = {'PickingId':$('#MarkPickingforOutBound-PickingId').val()};

            callMethodHandler('mark-pickingfor-outbound',me,params);
        });
        /* 3 OUTBOUND */
        $("#PrepareOutboundData").on('click',function() {
            var me = $(this);
            callMethodHandler('prepare-outbound-data',me,{});
        });
        /* 4 OUTBOUND */
        $("#GetOutBoundData").on('click',function() {
            var me = $(this),
                params = {'BatchId':$('#GetOutBoundData-BatchId').val()};
            callMethodHandler('get-outbound-data',me,params);
        });
        /* 5  OUTBOUND */
        $("#SendOutBoundFeedBackData").on('click',function() {
            console.log('CLICK ON #SendOutBoundFeedBackData');
            var me = $(this),
                params = {
                    'OutBoundId':$('#SendOutBoundFeedBackData-OutBoundId').val(),
                    'InBoundId':$('#SendOutBoundFeedBackData-InBoundId').val(),
                    'PackBarcode':$('#SendOutBoundFeedBackData-PackBarcode').val(),
                    'SkuBarcode':$('#SendOutBoundFeedBackData-SkuBarcode').val(),
                    'SkuQuantity':$('#SendOutBoundFeedBackData-SkuQuantity').val(),
                    'WaybillSerial':$('#SendOutBoundFeedBackData-WaybillSerial').val(),
                    'WaybillNumber':$('#SendOutBoundFeedBackData-WaybillNumber').val(),
                    'Volume':$('#SendOutBoundFeedBackData-Volume').val(),
                    'CargoShipmentNo':$('#SendOutBoundFeedBackData-CargoShipmentNo').val(),
                    'InvoiceNumber':$('#SendOutBoundFeedBackData-InvoiceNumber').val()
                };

            callMethodHandler('send-outbound-feedback-data',me,params);
        });

        /* 6 OUTBOUND */
        $("#MarkBatchforCompleted").on('click',function() {
            console.log('CLICK ON #MarkBatchforCompleted');
            var me = $(this),
                params = {'BatchId':$('#MarkBatchforCompleted-BatchId').val()};
            callMethodHandler('mark-batch-for-completed',me,params);
        });

        /* 1 RETURN */
        $("#GetInBoundDataForReturn").on('click',function() {
            var me = $(this);
            callMethodHandler('get-inbound-data-for-return',me,{});
        });

        /* 2 RETURN */
        $("#SendInBoundFeedBackDataForReturn").on('click',function() {
            var me = $(this),
                params = {
                    'InboundId':$('#SendInBoundFeedBackDataForReturn-InboundId').val(),
                    'AppointmentBarcode':$('#SendInBoundFeedBackDataForReturn-AppointmentBarcode').val(),
                    'PackBarcode':$('#SendInBoundFeedBackDataForReturn-PackBarcode').val(),
                    'SkuBarcode':$('#SendInBoundFeedBackDataForReturn-SkuBarcode').val(),
                    'SkuQuantity':$('#SendInBoundFeedBackDataForReturn-SkuQuantity').val(),
                };

            callMethodHandler('send-inbound-feedback-data-for-return',me,params);
        });



        /* -------------------------------------------------- */
        $("#GetBusinessUnitWMSData").on('click',function() {
            var me = $(this);
            callMethodHandler('get-business-unit-wms-data',me,{});
        });

        $("#GetMasterData").on('click',function() {
            var me = $(this),
                params = {
                    'ProcessRequestedDataType':$('#GetMasterData-ProcessRequestedDataType').val(),
                    'ShortCode':$('#GetMasterData-ShortCode').val(),
                    'SkuId':$('#GetMasterData-SkuId').val(),
                    'Ean':$('#GetMasterData-Ean').val()
                };
            callMethodHandler('get-master-data',me,params);
        });

        $("#GetSKUContentWMSData").on('click',function() {
            var me = $(this);
            callMethodHandler('get-sku-content-wms-data',me,{});
        });

        $("#CreateLcBarcode").on('click',function() {
            var me = $(this),
                params = {
                    'Count':$('#CreateLcBarcode-Count').val()
                };
            callMethodHandler('create-lc-barcode',me,params);
        });

        function sendPost(url,data) {

            $.post(url,data,function (data) {
                if(data.next) {
                    sendPost(url,{PageIndex: data.pageIndex,PageCount: data.pageCount});
                }

            }).fail(function () {
                console.log("server error");
            });
        }

        $("#GetMasterDataLoad").on('click',function() {
            var me = $(this),
                url = 'get-master-data-load',
                pageIndex = 0;
                pageCount = 0;

            console.log('CLICK ON '+me.attr('id'));
            me.html('Please wait');
            sendPost(url,{PageIndex: pageIndex,PageCount:pageCount});
        });
    });
</script>