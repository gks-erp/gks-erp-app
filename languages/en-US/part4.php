<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
//from DB or variables search like gks_lang($

if (isset($gks_lang_array['part4'])==false) 

  $gks_lang_array['part4']=array(

//gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr
'oikogeniaki_katastasti_descr'=>array(
  'Άγαμος'=>'Single',
  'Έγγαμος'=>'Married',
  'Διαζευγμένος'=>'Divorced',
  'Σε Χηρεία'=>'Widowed',  
),
  
//gks_eshop_fpa_base.fpa_base_descr
'fpa_base_descr'=>array(
  'Κανονικός'=>'Regular',
  'Μειωμένος'=>'Reduced',
  'Υπερμειωμένος'=>'Super-reduced',
  'Υπερ-υπερμειωμένος'=>'Super-super-reduced',
  'Χωρίς ΦΠΑ'=>'VAT excluded',
),

//getHotelReservationStatusDescr
'hotelreservationstatusdescr'=>array(
  'Σε καλάθι'=>'At Cart',
  'Πρόχειρη'=>'Draft',
  'Ακυρωμένη'=>'Cancelled',
  'Απορρίφθηκε'=>'Rejected',
  'Αναμονή Πληρωμής'=>'Wait payment',
  'Επιβεβαιωμένη'=>'Confirm',
  'Ολοκληρωμένη'=>'Completed',
  'Εξοφλημένη'=>'Payment',
),

//user roles getRoleDescrConv
'userroles'=>array(
  'Επισκέπτης/Επαφή'=>'Subscriber',
  'Διαχειριστής'=>'Administrator',
  'Διαχειριστής my' => 'Administrator my',
  'Συντάκτης'=>'Editor',
  'Συγγραφέας'=>'Author',
  'Συνεισφέρων'=>'Contributor',
  'Λογιστής'=>'Accountant',
  'HR Manager'=>'HR Manager',
  'Ομαδάρχης'=>'Team Leader',
  'Τεχνικός'=>'Technical',
  'Οδηγός'=>'Driver',
  'Υπεύθυνος Περιοχής'=>'Area Manager',
  'Χειριστής Μηχανημάτων'=>'Machine Operator',
  'Find Your Photos'=>'Find Your Photos',
  'Κλινικές'=>'Clinics',
  'Προμηθευτής'=>'Supplier',
  'Αποθηκάριος'=>'Warehousekeeper',
  'Καλλιτέχνης'=>'Artist',
  'Ταμίας'=>'Cashier',
  'Πελάτης'=>'Customer',
  'Διαχειριστής Παραγγελιών'=>'Order Manager',
  'Τιμολόγηση'=>'Invoicing',
  'Υπάλληλος'=>'Employee',
  'Πωλητής'=>'Salesperson',
  'Εξωτερικός Συνεργάτης'=>'External Associate',
  'Shop manager'=>'Shop manager',
  'SEO Manager'=>'SEO Manager',
  'SEO Editor'=>'SEO Editor',
  'b2b'=>'b2b',
  'B2B'=>'B2B',
),

//getHotelFolioStatusDescr
'hotelfoliostatusdescr'=>array(
  'Πρόχειρη' => 'Draft',
  'Ακυρωμένη' => 'Cancelled',
  'Ανοιχτή' => 'Open',
  'Ολοκληρωμένη' => 'Completed',
),

//getHotelAvailabilityDescr
'hotelavailabilitydescr'=>array(
  'Ανοιχτό'=>'Open',
  'Κλειστό'=>'Close',
),

//getHotelΑvailabilityDescr
'hotelαvailabilitydescr'=>array(
  'Ανοιχτό'=>'Open',
  'Κλειστό'=>'Close',
),

//getHotelRoomTypeStatusDescr
'hotelroomtypestatusdescr'=>array(
  'Ανενεργό'=>'Inactive',
  'Διαθέσιμο'=>'Available',
  'Ανακαίνιση'=>'Renovation',
),

//getHotelCustomTypeDescr
'hotelcustomtypedescr'=>array(
  'Προεπιλογή ξενοδοχείου'=>'Hotel default',
  'Προεπιλογή τύπου δωματίου'=>'Room default',
  'Ορισμός'=>'Set Value',
),

////gks_eshop_fiscal_position.fiscal_position_descr
// 'fiscal_position_descr'=>array(
  // 'Λιανικής Εσωτερικού'=>'Retail Domestic',
  // 'Λιανικής Ενδοκοινοτικές'=>'Retail EU',
  // 'Λιανικής Τρίτες Χώρες'=>'Retail Third Countries',
  // 'Λιανικής Εσωτερικού Μειωμένο'=>'Retail Domestic Reduced',
  // 'Χονδρικής Εσωτερικού'=>'Wholesale Domestic',
  // 'Χονδρικής Εσωτερικού (συνδεδεμένες οντότητες)'=>'Wholesale Domestic (affiliated entities)',
  // 'Χονδρικής Εσωτερικού Μειωμένο'=>'Wholesale Domestic Reduced',
  // 'Χονδρικής Εσωτερικού Μειωμένο (συνδεδεμένες οντότητες)'=>'Wholesale Domestic Reduced (affiliated entities)',
  // 'Χονδρικής Εσωτερικού Απαλλαγής'=>'Wholesale Domestic Exempt',
  // 'Χονδρικής Εσωτερικού Απαλλαγής (συνδεδεμένες οντότητες)'=>'Wholesale Domestic Exempt (affiliated entities)',
  // 'Χονδρικής Ενδοκοινοτικές'=>'Wholesale EU',
  // 'Χονδρικής Ενδοκοινοτικές (συνδεδεμένες οντότητες)'=>'Wholesale EU (affiliated entities)',
  // 'Χονδρικής Τρίτες Χώρες'=>'Wholesale Third Countries',
  // 'Χονδρικής Τρίτες Χώρες (συνδεδεμένες οντότητες)'=>'Wholesale Third Countries (affiliated entities)',
// ),


//gks_print_objects.object_descr
'object_descr'=>array(
  'Παραγγελίες'=>'Orders',
  'Παραστατικά'=>'Documents',
  'Πληρωμές'=>'Payments',
  'Δελτία'=>'Delivery Notes',
  'Κρατήσεις'=>'Bookings',
  'Transfer'=>'Transfer',
  'Εργασίες'=>'Tasks',
  'Είδη'=>'Products',
),

//gks_custom_field_type.field_type_name
'field_type_name'=> array(

  'Ναι/Όχι'=>'Yes/No',
  'Αριθμός ακέραιος'=>'Integer',
  'Αριθμός δεκαδικός'=>'Decimal',
  'Κείμενο'=>'Text',
  'Κείμενο μεγάλο'=>'Large Text',
  'Ημερομηνία'=>'Date',
  'Ώρα'=>'Time',
  'Ημερομηνία-Ώρα'=>'Date-Time',
  'Κείμενο μορφοποιημένο'=>'Formatted Text',
  'Τηλέφωνο'=>'Phone',
  'email'=>'email',
  'url'=>'url',
  'Επιλογή ενός από λίστα'=>'Select one from list',
  'Επιλογή πολλών από λίστα'=>'Select many from list',
  'Παραστατικό'=>'Invoice',
  'Ημερολόγιο'=>'Journal',
  'Σειρά'=>'Series',
  'Εταιρεία'=>'Company',
  'Υποκατάστημα'=>'Branch',
  'Ευκαιρία'=>'Opportunity',
  'Είδος'=>'Product',
  'Κατηγορία Είδους'=>'Product Category',
  'Ξενοδοχείο'=>'Hotel',
  'Διαθεσιμότητα'=>'Availability',
  'Όροφος'=>'Floor',
  'Τιμή δωματίου'=>'Room Price',
  'Κράτηση'=>'Reservation',
  'Δωμάτιο'=>'Room',
  'Τύπος δωματίου'=>'Room Type',
  'Παραγγελία'=>'Order',
  'Φόρμα Εκτύπωσης'=>'Print Form',
  'Εργασία παραγωγής'=>'Production Job',
  'Πόστο'=>'Post',
  'Ομάδα Επαφών'=>'Contact Group',
  'Αποθήκη'=>'Warehouse',
  'Επαφή'=>'Contact',
  'Πληρωμή'=>'Payment',
  'eshop'=>'eshop',
  'Μάρκα'=>'Brand',
  'Εργασία CRM'=>'Task CRM',
  'Συσκευή'=>'Device',
  'Περίσταση'=>'Occasion',
  'Συνταγή'=>'Recipe',
  'Επαφές'=>'Contacts',

),

'aade_invoicedeliverystatus'=>array(
  'Άγνωστο'=>'Unknown',
  'Το ΔΑ έχει εκδοθεί επιτυχώς (Registered)'=>'The Delivery Note has been successfully issued (Registered)',
  'Η διακίνηση έχει ξεκινήσει (InTransit)'=>'Transportation has started (InTransit)',
  'Ο μεταφορέας δήλωσε παράδοση (αναμονή επιβεβαίωσης από λήπτη B2B). (DeliveredByCarrier)'=>'The carrier has declared delivery (waiting for confirmation from B2B recipient). (DeliveredByCarrier)',
  'Η διακίνηση ολοκληρώθηκε με επιτυχία (Completed)'=>'Transportation has been successfully completed (Completed)',
  'Ο λήπτης απέρριψε την παραλαβή. (Rejected)'=>'The recipient has rejected the receipt. (Rejected)',
  'Ο εκδότης ακύρωσε το ΔΑ πριν την έναρξη της διακίνησης. (Cancelled)'=>'The issuer has cancelled the Delivery Note before the start of the transportation. (Cancelled)',
  'Ο μεταφορέας δήλωσε αποτυχία παράδοσης (FailedDelivery)'=>'The carrier has declared a failure of delivery (FailedDelivery)',
),

'aade_deliveryeventtype'=>array(
  'Έναρξη διακίνησης (RegisterTransfer)'=>'Start movement (RegisterTransfer)',
  'Δηλώση του αποτέλεσματος της παράδοσης (ConfirmOutcome)'=>'Declaration of the delivery outcome (ConfirmOutcome)',
  'Απόρριψη (Rejection)'=>'Rejection',
),

'aade_packagingtypedescr'=> array(
  'Παλέτα'=>'Pallet',
  'Κούτα'=>'Box',
  'Κιβώτιο'=>'Crate',
  'Βαρέλι'=>'Drum',
  'Σάκος'=>'Bag',
  'Λοιπά'=>'Other',
),
'aade_transporttypedescr'=> array(
  'Φορτηγό Δημόσιας Χρήσης'=>'Public Use Truck',
  'Φορτηγό Ιδιωτικής Χρήσης'=>'Private Use Truck',
  'Πλοίο'=>'Ship',
  'Τρένο'=>'Train',
  'Αεροπλάνο'=>'Airplane',
  'Λοιπά Μεταφορικά Μέσα (π.χ Δίκυκλα, ...)'=>'Other Means of Transport (e.g. Two-Wheeler, ...)',
  'Άνευ' => 'Without',
),

'aade_lch_outcome'=> array(
  'Πλήρες (FULL)'=>'FULL',
  'Ένα μέρος (PARTIAL)'=>'PARTIAL',
  'Τίποτα (NONE)'=>'NONE',
),

'getProductionLineStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Ακυρωμένο'=>'Cancelled',
  'Αναμονή'=>'Pending',
  'Προς Επεξεργασία'=>'To Progress',
  'Σε Επεξεργασία'=>'In Progress',
  'Σε Παύση'=>'Paused',
  'Απέτυχε'=>'Failed',
  'Ολοκληρωμένο'=>'Completed',
),

'getAccPayStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Ακυρωμένο'=>'Cancelled',
  'Καταχώρηση'=>'Listing',
  'Έκδοση'=>'Issue',
),
'getAccPayStateDescr_title_pre'=>array(
  'Πρόχειρη'=>'Draft',
  'Ακυρωμένη'=>'Cancelled',
  'Καταχωρημένη'=>'Listing',
),

'paroxos_signature_status_descr'=>array(
  'Νέα'=>'New',
  'Στάλθηκε σε POS'=>'Sent to POS',
  'Μπορεί να ξαναχρησιμοποιηθεί'=>'Can be reused',
  'Χρησιμοποιήθηκε'=>'Used',
  'Στάλθηκε στον πάροχο'=>'Sent to provider',
  'Ακυρώθηκε'=>'Cancelled',
),

'eftpos_transaction_status_descr'=>array(
  'Πρόχειρη'=>'Draft',
  'Στάλθηκε'=>'Sent',
  'Σε εξέλιξη'=>'In progress',
  'Ακυρώθηκε'=>'Cancelled',
  'Ματαιώθηκε'=>'Aborted',
  'Έγινε'=>'Done',
  'Άγνωστο'=>'Unknown',
),

'viva_status_descr'=>array(
  'Σε Αναμονή'=>'Waiting',
  'Σε εξέλιξη'=>'In progress',
  'Έγινε'=>'Done',
  'Σε αίτηση'=>'On request',
  'Ακυρώθηκε'=>'Cancelled',
  'Ματαιώθηκε'=>'Aborted',
),
'gks_eftpos_has_transaction_status_megeftpos'=>array(
  'Πρόχειρη'=>'Draft',
  'Στάλθηκε'=>'Sent',
  'Απέτυχε'=>'Failed',
  'Ολοκληρώθηκε'=>'Completed',
),
'gks_eftpos_has_transaction_status_cardlink'=>array(
  'Πρόχειρη'=>'Draft',
  'Στάλθηκε'=>'Sent',
  'Απέτυχε'=>'Failed',
  'Ολοκληρώθηκε'=>'Completed',
),


'getAADEstatuscodeDescr'=> array(
  'Επιτυχία'=>'Success',
  'Αποτυχία επιχειρησιακών ελέγχων'=>'Validation Error',
  'Τεχνικό σφάλμα'=>'Technical Error',
  'Σφάλμα επικύρωσης σύνταξης XML'=>'XML Syntax Error',
),

'getAccInvStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Ακυρωμένο'=>'Cancelled',
  'Προτιμολόγιο'=>'ProInvoice',
  'Υπό Έκδοση'=>'For Ιssue',
  'Καταχώρηση'=>'Listing',
  'Έκδοση'=>'Ιssue',
  'Εξοφλημένο'=>'Paid',
),
'getAccInvStateDescr_title_pre'=>array(
  'Πρόχειρο'=>'Draft',
  'Ακυρωμένο'=>'Cancelled',
  'Προτιμολόγιο'=>'ProInvoice',
  'Καταχωρημένο'=>'Listing',
),
'getProjectStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Σε εξέλιξη'=>'In progress',
  'Ολοκληρωμένο'=>'Completed',
  'Ακυρωμένο'=>'Cancelled',
),
'getGenericStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Ολοκληρωμένο'=>'Completed',
  'Ακυρωμένο'=>'Cancelled',
),

'getTaskStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Σάρωση'=>'Scan',
  'Έλεγχος 1'=>'Check 1',
  'Επεξεργασία'=>'Edit',
  'OCR-PDF'=>'OCR-PDF',
  'Έλεγχος 2'=>'Check 2',
  'Ολοκληρωμένο'=>'Completed',
  'Δημόσιο'=>'Public',
  'Ακυρωμένο'=>'Cancelled',
),

'getActivityStatusDescr'=>array(
  'Νέα'=>'New',
  'Έγινε'=>'Done',
  'Άκυρο'=>'Cancel',
),

'gks_product_base_type_descr'=>array(
  'Εμπόρευμα'=>'Commodity',
  'Προϊόν'=>'Product',
  'Υπηρεσία'=>'Service',
  'Εμ'=>'Co',
  'Πρ'=>'Pr',
  'Υπ'=>'Se',
),

'gks_woo_order_state_descr'=>array(
  'Εκκρεμεί πληρωμή'=>'Pending',
  'Σε επεξεργασία'=>'Processing',
  'Σε αναμονή'=>'On-Hold',
  'Ολοκληρωμένη'=>'Completed',
  'Ακυρωμένη'=>'Cancelled',
  'Επιστροφή χρημάτων'=>'Refunded',
  'Αποτυχημένη'=>'Failed',
),
'getWhiMovStateDescr'=>array(
  'Πρόχειρο'=>'Draft',
  'Ακυρωμένο'=>'Cancelled',
  'Καταχώρηση'=>'Listing',
  'Έκδοση'=>'Issue',
  'Κλεισμένο'=>'Closed',
),
'getWhiMovStateDescr_title_pre'=>array(
  'Πρόχειρο'=>'Draft',
  'Ακυρωμένο'=>'Cancelled',
  'Καταχωρημένο'=>'Listing',
),

'getOrderStateDescr'=>array(
  'Σε καλάθι'=>         'Cart',            
  'Πρόχειρη'=>          'Draft',           
  'Σε Αναμονή'=>        'Pending',         
  'Προσφορά'=>          'Offer',           
  'Προς Ακύρωση'=>      'For Cancellation',
  'Ακυρωμένη'=>         'Cancelled',       
  'Απορρίφθηκε'=>       'Rejected',        
  'Αναμονή Πληρωμής'=>  'Wait Payment',    
  'Καταχωρημένη'=>      'Registered',      
  'Σε παραγωγή'=>       'In Production',   
  'Απέτυχε'=>           'Failed',          
  'Προς Παράδοση'=>     'In Delivery',     
  'Εκτελέστηκε'=>       'Execute',         
  'Ολοκληρωμένη'=>      'Completed',       
  'Εξοφλημένη'=>        'Payment',         
),



//get_assets_whi_mov_descr
//check sto delete ta : gks_lang('δεν είναι σε κατάσταση')


);

