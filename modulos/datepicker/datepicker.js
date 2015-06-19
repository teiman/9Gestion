

var oeDatePicker = function () { }
 
/**
*   Static variables
*/


/** The popup window containing the picker */
 oeDatePicker.gPopup  = null;
 
 
/** The original starting date and currently selected date */
 oeDatePicker.gOriginalDate = null; 
 oeDatePicker.gSelectedDate = null; 
 
 /* selected items */
 oeDatePicker.gSelectedMonthItem = null;
 oeDatePicker.gSelectedDayItem = null;
 
 
 
/**
*   Set up the picker, called when the popup pops
*/

oeDatePicker.onpopupshowing = function( popup )
{
   // remember the popup item so we can close it when we are done
   
   oeDatePicker.gPopup = popup;
   
   // get the start date from the popup value attribute and select it
   
   var startDate = popup.getAttribute( "value" );
   
   oeDatePicker.gOriginalDate = new Date( startDate );
   oeDatePicker.gSelectedDate = new Date( startDate );
   
   // draw the year based on the selected date
   
   oeDatePicker.redrawYear();
   
   // draw the month based on the selected date
   
   var month = oeDatePicker.gSelectedDate.getMonth() + 1;
   var selectedMonthBoxItem = document.getElementById( "oe-date-picker-year-month-" + month + "-box"  );
   
   oeDatePicker.selectMonthItem( selectedMonthBoxItem );
   
   // draw in the days for the selected date
   
   oeDatePicker.redrawDays();
}
 
 
/**
*   Called when a day is clicked, close the picker and call the client's oncommand
*/
 
 
oeDatePicker.clickDay = function( newDayItemNumber )
{
   // get the clicked day
     
   var dayNumberItem = document.getElementById( "oe-date-picker-month-day-text-" + newDayItemNumber );
   
   var dayNumber = dayNumberItem.getAttribute( "value" );
   
   // they may have clicked an unfilled day, if so ignore it and leave the picker up
   
   if( dayNumber != "" )
   {
      // set the selected date to what they cliked on
      
      oeDatePicker.gSelectedDate.setDate( dayNumber );
      
      oeDatePicker.selectDate();

      oeDatePicker.gPopup.hidePopup();
   }
}


oeDatePicker.selectDate = function()
{
   // We copy the picked date to avoid problems with changing the Date object in place
      
   var pickedDate = new Date( oeDatePicker.gSelectedDate );
   
   // put the selected date in the popup item's value property
   
   oeDatePicker.gPopup.value = pickedDate;
   
   // get the client oncommand function, call it if there is one
   
   var commandEventMethod = oeDatePicker.gPopup.getAttribute( "oncommand" );
   
   if( commandEventMethod != null )
   {
      // set up a variable date, that will be avaialable from within the 
      // client method
   
      var date = pickedDate;
   
      // Make the function a member of the popup before calling it so that 
      // 'this' will be the popup
      
      oeDatePicker.gPopup.oeDatePickerFunction =  function() { eval( commandEventMethod ); };
      
      oeDatePicker.gPopup.oeDatePickerFunction();
   }
   
   // close the popup
   
   //oeDatePicker.gPopup.closePopup ();

}


/**
* Called when a month box is clicked 
*/

oeDatePicker.clickMonth = function( newMonthItem, newMonthNumber )
{
   // already selected, return
   
   if( oeDatePicker.gSelectedMonthItem  == newMonthItem )
   {
      return;
   }

   // Avoid problems when changing months if the date is at the end of the month
   // i.e. if date is 31 march and you do a setmonth to april, the month would
   // actually be set to may, beacause april only has 30 days.
   // This is why we keep the original date around.

   var oldDate = oeDatePicker.gSelectedDate.getDate();
   var yearNumber = oeDatePicker.gSelectedDate.getFullYear();
   
   var lastDayOfMonth = DateUtils.getLastDayOfMonth( yearNumber, newMonthNumber-1 );

   if ( oldDate > lastDayOfMonth )
   {
       oeDatePicker.gSelectedDate.setDate(lastDayOfMonth);
   }
   
   // update the selected date
   
   oeDatePicker.gSelectedDate.setMonth( newMonthNumber - 1 );
   
   // select Month
   
   oeDatePicker.selectMonthItem( newMonthItem );
 
   // redraw days
   
   oeDatePicker.redrawDays();

   oeDatePicker.selectDate();
}
 

/**
* Called when previous Year button is clicked 
*/

oeDatePicker.previousYearCommand = function()
{
   // update the selected date
   
   var oldYear = oeDatePicker.gSelectedDate.getFullYear(); 
   oeDatePicker.gSelectedDate.setFullYear( oldYear - 1 ); 
   
   // redraw the year and the days
   
   oeDatePicker.redrawYear();
   oeDatePicker.redrawDays();

   oeDatePicker.selectDate();
}


/**
* Called when next Year button is clicked 
*/

oeDatePicker.nextYearCommand = function()
{
   // update the selected date
   
   var oldYear = oeDatePicker.gSelectedDate.getFullYear(); 
   oeDatePicker.gSelectedDate.setFullYear( oldYear + 1 ); 
   
   // redraw the year and the days
   
   oeDatePicker.redrawYear();
   oeDatePicker.redrawDays();

   oeDatePicker.selectDate();
}
 

/**
* Draw the year based in the selected date 
*/

oeDatePicker.redrawYear = function()
{
   var yearTitleItem = document.getElementById( "oe-date-picker-year-title-text" );
   yearTitleItem.setAttribute( "value", oeDatePicker.gSelectedDate.getFullYear() );
}


/**
* Select a month box 
*/

oeDatePicker.selectMonthItem = function( newMonthItem )
{
   // clear old selection, if there is one
   
   if( oeDatePicker.gSelectedMonthItem != null )
   {
      oeDatePicker.gSelectedMonthItem.setAttribute( "selected" , false );
   }

   // Set the selected attribute, used to give it a different style
   
   newMonthItem.setAttribute( "selected" , true );
      
   // Remember new selection
  
  oeDatePicker.gSelectedMonthItem = newMonthItem;

}


/**
* Select a day box 
*/

oeDatePicker.selectDayItem = function( newDayItem )
{
   // clear old selection, if there is one
   
   if( oeDatePicker.gSelectedDayItem != null )
   {
      oeDatePicker.gSelectedDayItem.setAttribute( "selected" , false );
   }

   if( newDayItem != null )
   {
      // Set the selected attribute, used to give it a different style
      
      newDayItem.setAttribute( "selected" , true );
   }
         
   // Remember new selection
      
   oeDatePicker.gSelectedDayItem = newDayItem;

}


/**
* Redraw day numbers based on the selected date
*/

oeDatePicker.redrawDays = function( )
{
  // Write in all the day numbers
   
   var firstDate = new Date( oeDatePicker.gSelectedDate.getFullYear(), oeDatePicker.gSelectedDate.getMonth(), 1 );
   var firstDayOfWeek = firstDate.getDay();
   
   var lastDayOfMonth = DateUtils.getLastDayOfMonth( oeDatePicker.gSelectedDate.getFullYear(), oeDatePicker.gSelectedDate.getMonth() )
   
   
   // clear the selected day item
   
   oeDatePicker.selectDayItem( null );
   
   // redraw each day bax in the 7 x 6 grid
   
   var dayNumber = 1;
   
   for( var dayIndex = 0; dayIndex < 42; ++dayIndex )
   {
      // get the day text box
      
      var dayNumberItem = document.getElementById( "oe-date-picker-month-day-text-" + (dayIndex + 1) );
      
      // if it is an unfilled day ( before first or after last ), just set its value to "",
      // and don't increment the day number.
      
      if( dayIndex < firstDayOfWeek || dayNumber > lastDayOfMonth )
      {
         dayNumberItem.setAttribute( "value" , "" );  
      }
      else
      {
         // set the value to the day number
         
         dayNumberItem.setAttribute( "value" , dayNumber );
         
         // draw the day as selected
         if( dayNumber == oeDatePicker.gSelectedDate.getDate() ) 
         {
            var dayNumberBoxItem = document.getElementById( "oe-date-picker-month-day-" + (dayIndex + 1) + "-box"  );
            oeDatePicker.selectDayItem( dayNumberBoxItem );
         }
   
         // advance the day number
         
         ++dayNumber;  
      }
   }
   
 }

