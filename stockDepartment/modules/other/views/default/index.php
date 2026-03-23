<?php
use yii\helpers\Html;

?>
<iframe class="frame-a4-test- frame-bl-test" style="display: none-" name="frame-a4" src="#"></iframe>
<iframe class="frame-bl-test" style="display: none-" name="frame-bl" src="#"></iframe>

<br />
<span id="print-test" style="cursor: pointer;">PRINT HTML TEST</span>

<script type="text/javascript">
    $(function () {
        $('#print-test').on('click', function () {
            testPrint();
        });
//        console.info(jsPrintSetup);
//        var printers = jsPrintSetup.getPrintersList().split(',');
//        console.info(printers);
    });


    function testPrint() {
//        (function(){
//            'use strict';
// only do something if the plugin is live
        if (typeof 'jsPrintSetup' != 'undefined') {
            var printers = jsPrintSetup.getPrintersList().split(','); // get a list of installed printers

            var computers = {
                'k1_A4': "\\\\192.168.1.6\\Xerox Phaser 3320",
                'k1_BL': "\\\\192.168.1.4\\TSC TDP-224",

                'k2_A4': "Xerox Phaser 3320",
                'k2_BL': "\\\\192.168.1.4\\TSC TDP-224",

                'k3_A4': "\\\\192.168.1.6\\Xerox Phaser 3320",
                'k3_BL': "TSC TDP-224"
            };
            var A4p = '',
                BLp = '',
                printer = '';

            for (var k in printers) {

                printer = printers[k];

                switch (printer) {
                    case "\\\\192.168.1.2\\Samsung M2070 Series":
                    case "\\\\192.168.1.6\\Xerox Phaser 3320":
                    case "\\\\192.168.1.6\\Xerox Phaser 3320 (Копия 1)":
                    case "Xerox Phaser 3320":
                        A4p = printer;
                        break;
                    case "\\\\192.168.1.4\\TSC TDP-244":
                    case "TSC TDP-244":
                        BLp = printer;
                        break;
                }
                console.info('printer : ' + printer);
                console.info('A4 : ' + A4p);
                console.info('BL : ' + BLp);
            }

            console.info('A4 : ' + A4p);
            console.info('BL : ' + BLp);
            console.log('getPrintersList :' + printers);

            $.post('/other/default/save-printers', {printers: printers}, function () {
                console.info('send printers to server YPA');
            });

            if (A4p != '') {
                console.log("A4 xo");
                jsPrintSetup.clearSilentPrint();
                jsPrintSetup.refreshOptions();
                jsPrintSetup.setPrinter(A4p);
                jsPrintSetup.setSilentPrint(0); // 0 -show print settings dialog, 1 - not show print settings dialog
                jsPrintSetup.setOption('orientation', '1'); // 1 - kLandscapeOrientation 0 - kPortraitOrientation
//                jsPrintSetup.setOption('paperData','11');
                jsPrintSetup.setPaperSizeUnit(1);
//                jsPrintSetup.setOption('paperHeight',128);
//                jsPrintSetup.setOption('paperWidth',77);
                jsPrintSetup.definePaperSize(119,119,'nmdx_boxl','nmdx_bl700x400mm','Nmdx Box Label 700x400mm',77,128,'1'); //
                jsPrintSetup.setOption('paperData','119') ;
                jsPrintSetup.setGlobalOption('paperData','119');
                jsPrintSetup.setPaperSizeData('119');

                jsPrintSetup.setOption('headerStrLeft', '');
                jsPrintSetup.setOption('headerStrCenter', '');
                jsPrintSetup.setOption('headerStrCenter', '');
                jsPrintSetup.setOption('headerStrRight', '');
                jsPrintSetup.setOption('footerStrLeft', '');
                jsPrintSetup.setOption('footerStrCenter', '');
                jsPrintSetup.setOption('footerStrRight', '');
                window.frames['frame-a4'].location.href = "/other/default/a4";

                $(window.frames['frame-a4']).ready(function () {
                    setTimeout(function () {
                        console.info('ready A4 printWindow begin');

                        var cPsD = jsPrintSetup.getPaperSizeData();
                        console.info('getPaperSizeData :' + cPsD);
                        var cpsl = jsPrintSetup.getPaperSizeList();
                        console.info('getPaperSizeList :' + cpsl);
                        console.info('getPaperMeasure :' + jsPrintSetup.getPaperMeasure());

                        jsPrintSetup.printWindow(window.frames['frame-a4']);
                        jsPrintSetup.setSilentPrint(0);
                        console.info('ready A4 printWindow end');
                    }, 1500);
                });
            }

            if (BLp != '') {
                console.log("BL xo");
                jsPrintSetup.clearSilentPrint();
                jsPrintSetup.refreshOptions();
                jsPrintSetup.setPrinter(BLp);
                jsPrintSetup.setSilentPrint(1); // 0 -show print settings dialog, 1 - not show print settings dialog
                jsPrintSetup.setOption('orientation', '1'); // 1 - kLandscapeOrientation 0 - kPortraitOrientation

                jsPrintSetup.setOption('headerStrLeft', '');
                jsPrintSetup.setOption('headerStrCenter', '');
                jsPrintSetup.setOption('headerStrCenter', '');
                jsPrintSetup.setOption('headerStrRight', '');
                jsPrintSetup.setOption('footerStrLeft', '');
                jsPrintSetup.setOption('footerStrCenter', '');
                jsPrintSetup.setOption('footerStrRight', '');
                window.frames['frame-bl'].location.href = "/other/default/bl";

                $(window.frames['frame-bl']).ready(function () {
                    setTimeout(function () {
                        console.info('ready BL printWindow begin');
                        jsPrintSetup.printWindow(window.frames['frame-bl']);
                        jsPrintSetup.setSilentPrint(0);
                        console.info('ready BL printWindow end');
                    }, 1500);
                });
            }

            return 0;

            console.log('getPrintersList :' + printers);
//                return 0;
            jsPrintSetup.clearSilentPrint();
            jsPrintSetup.refreshOptions();
//                jsPrintSetup.definePaperSize(45,45,'nmdx_box_label74','nmdx_box_label700x400mm','nmdx Box Label 700x400mm',80,130,'1'); //
//                jsPrintSetup.setOption('paperData','45') ;
//                jsPrintSetup.setGlobalOption('paperData','45');
            jsPrintSetup.setPrinter("\\\\192.168.1.2\\Samsung M2070 Series");
            // no print dialogue boxes needed
            jsPrintSetup.setSilentPrint(0); // 0 -show print settings dialog, 1 - not show print settings dialog
            jsPrintSetup.setOption('orientation', '1'); // 1 - kLandscapeOrientation 0 - kPortraitOrientation
//                jsPrintSetup.setGlobalOption('orientation','0');
//                jsPrintSetup.setOption('orientation','kPortraitOrientation');
//                jsPrintSetup.setOption('paperData','11'); // A5 - 11 A4 - 9
//                jsPrintSetup.definePaperSize(119,119,'nmdx_boxl','nmdx_bl700x400mm','Nmdx Box Label 700x400mm',80,130,'1'); //
//                jsPrintSetup.setOption('paperData','119') ;
//                jsPrintSetup.setGlobalOption('paperData','119');
//                jsPrintSetup.setPaperSizeData('119');
// jsPrintSetup.setOption('paperSizeType','A3');
//                jsPrintSetup.setOption('paperData','9');
//                jsPrintSetup.setOption('paperSizeType','15');
//                jsPrintSetup.setGlobalOption('paperSizeType','15');
//                jsPrintSetup.setOption('orientation', 'kPortraitOrientation');
            jsPrintSetup.setOption('headerStrLeft', '');
            jsPrintSetup.setOption('headerStrCenter', '');
            jsPrintSetup.setOption('headerStrCenter', '');
            jsPrintSetup.setOption('headerStrRight', '');
            jsPrintSetup.setOption('footerStrLeft', '');
            jsPrintSetup.setOption('footerStrCenter', '');
            jsPrintSetup.setOption('footerStrRight', '');

//                var paperLists = jsPrintSetup.getPaperSizeList();
//                var getOptionOrientation = jsPrintSetup.getOption('orientation');
            var cPsD = jsPrintSetup.getPaperSizeData();
//                var c = jsPrintSetup.getOption('paperSizeUnit');
            var cpsl = jsPrintSetup.getPaperSizeList();
//                var getGlobalOptionOrientation = jsPrintSetup.getGlobalOption('orientation');
            var c = jsPrintSetup.getPaperSizeDataByID('45');

            console.info('getPaperSizeDataByID45 :' + c);
            console.info('getPaperSizeList :' + cpsl);
            console.info('getPaperSizeData :' + cPsD);
            console.info('getPaperMeasure :' + jsPrintSetup.getPaperMeasure());
//                console.info('getPaperSizeList :'+c);
//                console.info('paperSizeUnit :'+c);
//                console.info(getGlobalOptionOrientation);

//            jsPrintSetup.setOption('toFileName', "file:\\\\C:\\Users\\kitavrus\\Downloads\\1433914482-colins-allocate-list.pdf");
//            jsPrintSetup.setOption('toFileName', "C:\\Users\\kitavrus\\Downloads\\1433914482-colins-allocate-list.pdf");
//            jsPrintSetup.setOption('toFileName', "file:///C:/Users/kitavrus/Downloads/1433914482-colins-allocate-list.pdf");
//            jsPrintSetup.setOption('toFileName', "/other/default/pdf");
//            jsPrintSetup.setSilentPrint(0);
//            jsPrintSetup.setOption('headerStrCenter', 'bla bla frame');
//            jsPrintSetup.setOption('headerStrRight', '');
            // here window is current frame!
//            jsPrintSetup.printWindow(window);
//            jsPrintSetup.print();
//            window.location.reload('/other/default/pdf');
//                window.frames['frame-test'].src = "file:///C:/Users/kitavrus/Downloads/1433914482-colins-allocate-list.pdf";
//                window.frames['frame-test'].location.reload("/other/default/pdf");
//                window.frames['frame-test'].location.href = "/other/default/pdf";

//                window.frames['frame-test'].location.href = "file:///C://Users/kitavrus//Downloads//1433914482-colins-allocate-list.pdf";
//                jsPrintSetup.printWindow(window.frames['frame-test']);
            window.frames['frame-test'].location.href = "/other/default/pdf";
            window.frames['frame-test'].onload = function () {
                console.info('onLoad printWindow frames');
                jsPrintSetup.printWindow(window.frames['frame-test']);
                jsPrintSetup.setSilentPrint(0);
            };
//

//            jsPrintSetup.setSilentPrint(0);
//                console.info(printers);

//            printers.each(function(e) {
//                console.info(e);
//            });
            // DELETE FROM `wms20`.`inbound_orders` WHERE `inbound_orders`.`consignment_inbound_order_id` = 12
//


            /*            printers.each(function(el){
             console.info(el);
             //                new Element('option', {
             //                    value: el,
             //                    text: el,
             //                    selected: defaultPrinter === el
             //                }).inject(printList);

             });*/

            // create a dropdown
            /*            var printList = new Element('select#printerList', {
             events: {
             change: function(){
             Cookie.write('defaultPrinter', this.get('value'), {
             path: '/admin/',
             duration: 365
             });

             // save the new printer selection
             jsPrintSetup.setPrinter(this.get('value'));
             }
             }
             });*/

            // get default by preference if any
            /*            var defaultPrinter = Cookie.read('defaultPrinter');

             printers.each(function(el){
             new Element('option', {
             value: el,
             text: el,
             selected: defaultPrinter === el
             }).inject(printList);

             });*/

            // needs 2 dom els: #printSetup and #goPrint
            /*            printList.inject(document.id('printSetup'), 'top');

             // attach an event that prints from an element called goPrint
             document.id('goPrint').addEvents({
             click: function(){
             jsPrintSetup.print();
             }
             });*/
        }

//        }).call(this);
    }


</script>
