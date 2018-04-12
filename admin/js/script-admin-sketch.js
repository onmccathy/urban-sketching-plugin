/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function () {
    jQuery('.sket-start-date').flatpickr({
        dateFormat: 'Y-m-d',
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        onChange: function (d) {
            jQuery(".sket-end-date").flatpickr().set("minDate", d.fp_incr(1));
        }
    }
    );
    jQuery('.sket-end-date').flatpickr({
        dateFormat: 'Y-m-d',
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        onChange: function (d) {
               jQuery(".sket-start-date").flatpickr().set("maxDate", d);
        }
        
    }
    );
    jQuery('#sket_public_price_field,#sket_member_price_field').change( function(){
       
        if(!jQuery.isNumeric(jQuery(this).val()))  {
            alert("Public Price and Member price must be numeric");
        }
    });
    
});
jQuery(document).ready(function () {
    jQuery('.sket-timepicker').flatpickr({
        enableTime: true,
        noCalendar: true,
        minuteIncrement: 15,
    });
});