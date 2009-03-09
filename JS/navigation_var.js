/***********************************************************************************
*	(c) Ger Versluis 2000 version 5.411 24 December 2001 (updated Jan 31st, 2003 by Dynamic Drive for Opera7)
*	For info write to menus@burmees.nl		          *
*	You may remove all comments for faster loading	          *		
***********************************************************************************/

	var NoOffFirstLineMenus=5;						// Number of first level items
	var LowBgColor='white';							// Background color when mouse is not over
	var LowSubBgColor='CCFFFF';						// Background color when mouse is not over on subs
	var HighBgColor='CCFFFF';						// Background color when mouse is over
	var HighSubBgColor='99FFFF';						// Background color when mouse is over on subs
	var FontLowColor='003399';						// Font color when mouse is not over
	var FontSubLowColor='black';					// Font color subs when mouse is not over
	var FontHighColor= '003399';						// Font color when mouse is over
	var FontSubHighColor='black';					// Font color subs when mouse is over
	var BorderColor='white';						// Border color
	var BorderSubColor='CCFFFF';					// Border color for subs
	var BorderWidth=0;								// Border width
	var BorderBtwnElmnts=0;							// Border between elements 1 or 0
	var FontFamily="gill sans, arial,verdana, sans serif"	// Font family menu items
	var FontSize=7;									// Font size menu items
	var FontBold=1;									// Bold menu items 1 or 0
	var FontItalic=0;								// Italic menu items 1 or 0
	var MenuTextCentered='left';					// Item text position 'left', 'center' or 'right'
	var MenuCentered='left';						// Menu horizontal position 'left', 'center' or 'right'
	var MenuVerticalCentered='top';					// Menu vertical position 'top', 'middle','bottom' or static
	var ChildOverlap=.2;							// horizontal overlap child/ parent
	var ChildVerticalOverlap=.2;					// vertical overlap child/ parent
	var StartTop=2;								// Menu offset x coordinate
	var StartLeft=290;								// Menu offset y coordinate
	var VerCorrect=0;								// Multiple frames y correction
	var HorCorrect=0;								// Multiple frames x correction
	var LeftPaddng=3;								// Left padding
	var TopPaddng=2;								// Top padding
	var FirstLineHorizontal=1;						// SET TO 1 FOR HORIZONTAL MENU, 0 FOR VERTICAL
	var MenuFramesVertical=1;						// Frames in cols or rows 1 or 0
	var DissapearDelay=1000;						// delay before menu folds in
	var TakeOverBgColor=1;							// Menu frame takes over background color subitem frame
	var FirstLineFrame='navig';						// Frame where first level appears
	var SecLineFrame='space';						// Frame where sub levels appear
	var DocTargetFrame='space';						// Frame where target documents appear
	var TargetLoc='';								// span id for relative positioning
	var HideTop=0;									// Hide first level when loading new document 1 or 0
	var MenuWrap=1;									// enables/ disables menu wrap 1 or 0
	var RightToLeft=0;								// enables/ disables right to left unfold 1 or 0
	var UnfoldsOnClick=0;							// Level 1 unfolds onclick/ onmouseover
	var WebMasterCheck=0;							// menu tree checking on or off 1 or 0
	var ShowArrow=0;								// Uses arrow gifs when 1
	var KeepHilite=1;								// Keep selected path highligthed
	var Arrws=['tri.gif',5,10,'tridown.gif',10,5,'trileft.gif',5,10];	// Arrow source, width and height

function BeforeStart(){return}
function AfterBuild(){return}
function BeforeFirstOpen(){return}
function AfterCloseAll(){return}


// Menu tree
//	MenuX=new Array(Text to show, Link, background image (optional), number of sub elements, height, width);
//	For rollover images set "Text to show" to:  "rollover:Image1.jpg:Image2.jpg"

Menu1=new Array("About Us |","http://www.energytrust.org/Pages/about/","",5,17,53);
	Menu1_1=new Array("Who We Are","http://www.energytrust.org/Pages/about/who_we_are/index.html","",0,17,102);
	Menu1_2=new Array("Current Activities","http://www.energytrust.org/Pages/about/activities/index.html","",0);
	Menu1_3=new Array("Energy Efficiency","http://www.energytrust.org/Pages/about/efficiency/","",0);
	Menu1_4=new Array("Renewable Energy","http://www.energytrust.org/Pages/about/renewables/","",0);
	Menu1_5=new Array("Library","http://www.energytrust.org/Pages/about/library/","",0);
	

Menu2=new Array("Business Programs |","http://www.energytrust.org/Pages/business_services/","",3,17,100);
	Menu2_1=new Array("Building Efficiency","http://www.energytrust.org/Pages/business_services/building_eff/","",0,17,125);
	Menu2_2=new Array("New Building Efficiency","http://www.energytrust.org/Pages/business_services/new_building_eff/","",0);
	Menu2_3=new Array("Manufacturing Efficiency","http://www.energytrust.org/Pages/business_services/manufacturing/","",0);
	
	
Menu3=new Array("Home Energy Programs |","http://www.energytrust.org/Pages/home_services/","",2,17,119);
	Menu3_1=new Array("Home Energy Savings","http://www.energytrust.org/Pages/home_services/home/","",0,17,130);
	Menu3_2=new Array("New Home Energy Savings","http://www.energytrust.org/Pages/home_services/new_home/","",0);
	
Menu4=new Array("Renewable Programs |", "http://www.energytrust.org/Pages/renewable_energy_programs/","",3,17,110);
	Menu4_1=new Array("Solar","http://www.energytrust.org/Pages/renewable_energy_programs/solar/pv.html","",0,17,100);
	Menu4_2=new Array("Wind","http://www.energytrust.org/Pages/renewable_energy_programs/wind/anemometer.html","",0);
	Menu4_3=new Array("Open Solicitation","http://www.energytrust.org/Pages/renewable_energy_programs/open_solicitation/index.html","",0);
	
Menu5=new Array("Opportunities","http://www.energytrust.org/Pages/opportunities/","",3,17,100);
	Menu5_1=new Array("Trade Allies","http://www.energytrust.org/Pages/trade_allies/index.html","",0,17,100);
	Menu5_2=new Array("RFPs & RFQs","http://www.energytrust.org/Pages/opportunities/rfps/rfps.html","",0);
	Menu5_3=new Array("Job Opportunities","http://www.energytrust.org/Pages/opportunities/jobs/jobs.html","",0);

