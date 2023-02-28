$ ( function () {
	$( document ).on ( "click" ,"#skin_type,#salary",function () {
		$( ".fileMask" ).css ( "display" , "block" );
	} );
	$("#skin_type").click ( function () {
		$(".picker-modal1" ).css( "display" , "block" );
		//$(".my-edit").css("position","fixed");
		$(".picker-modal1 > div" ).click( function () {
			$ ( ".picker-modal1 > div" ).removeClass ( "picker-selected" );
			$ ( this ).addClass ( "picker-selected" );
			$ ( "#skin_type").val ( $ ( this ).attr ( "data-picker-value" ) );
			$ ( "#skin_type").attr ( "value" , $ ( this ).attr ( "data-picker-value" ) );
			$ ( "#skin_type").attr ( "value_type" , $ ( this ).attr ( "value_type" ) );
			$ ( ".picker-modal1" ).css ( "display" , "none" );
			$ ( ".fileMask" ).css ( "display" , "none" );
			//$(".my-edit").css("position","static");
		} );
	} );


//收入
	$("#salary" ).click(function (){
		$(".picker-modal3" ).css( "display" , "block" );
		//$(".my-edit").css("position","fixed");
		$( ".picker-modal3 > div").click( function () {
			$ ( ".picker-modal3 > div").removeClass( "picker-selected");
			$ ( this ).addClass("picker-selected" );
			$ ( "#salary").val($( this).attr( "data-picker-value"));
			$ ( "#salary").attr("value_type",$( this).attr( "value_type"));
			$ ( ".picker-modal3").css( "display" ,"none");
			$ ( ".fileMask").css("display" , "none");
			//$(".my-edit").css("position","static");
		} );
	} );

} );
