 //Agrega un método JQuery Validator para validar los password
 $.validator.addMethod('validPassword',
 function(value, element, param) 
 {
     if (value != '') { 
         if(value.match(/.*[a-z]+.*/i) == null) {
             return false;
         }
         if(value.match(/.*\d+.*/) == null) {
             return false;
         }
     }
     return true;
 },
 'Debe contener al menos una letra y un número'
);