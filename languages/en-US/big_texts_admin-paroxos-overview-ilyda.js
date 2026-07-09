/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

document.getElementById('invoices_no_mark_text').innerHTML =
'These are the documents that have been sent to the provider but have not been sent by the provider to myData, possibly because myData\'s systems have a problem.<br>'+
'The <b>Pending from provider to IAPR</b> list displays the documents that are in the queue for sending to the provider.<br>'+
'The <b>Get list from provider</b> button receives the list of documents that are in the queue in real time.<br>'+
'When they are sent from the provider to myData, you should go to these documents and do <span class="paroxos_get_docstate"><i class="fas fa-sync"></i></span> <b> Get status from provider</b> from the relevant button of each document.';


document.getElementById('invoices_tf1_text').innerHTML =
'These are the documents that have not been sent to the provider, possibly the provider\'s systems have a problem.<br>'+
'<b>However</b> a QR Code has been created with a link to the provider and can be used on the printout that you will need to give to the recipient.<br>'+
'The recipient will be able to see the basic elements of this document.<br>'+
'You, on your part, should send the document to the provider within 24 hours, when communication between gks ERP and the provider is restored.<br>'+
'In order to be able to create this QR Code, there must be an <b>active</b> <span class="tf1_local_ACTIVE">ACTIVE</span> key for each VAT number.';

