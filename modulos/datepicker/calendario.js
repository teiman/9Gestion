
var destinoDate = "start-date-text";

function EnviaCalendario( datepicker , seraDestino ){ //Enviador
	destinoDate = seraDestino;
	var datePickerPopup = document.getElementById( datepicker );//"oe-date-picker-popup" );
	datePickerPopup.setAttribute( "value", new Date() );	
}

function RecibeCalendario( datepopup ){ //Receptor
	var newDate =  datepopup.value;
	var tempSrc = document.getElementById(destinoDate);
	var getMonth = newDate.getMonth() + 1;
	
	tempSrc.value=newDate.getDate() + "-" + getMonth + "-" +  newDate.getFullYear();
}