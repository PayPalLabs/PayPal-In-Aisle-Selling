var orderDataArr = [];

window.onpageshow = function(evt) {
	if (evt.persisted) {
		$('#mask').css("left","-200%");
    }
};

function gotoURL(urlStr) {
	$('#mask-text').html('Loading');
	$('#mask').css("left","0px");
	window.location.href = urlStr;
}


function goBack(){
	//$('#mask-text').html('Loading');
	//$('#mask').css("left","0px");
	history.back();
	//$('#mask').css("left","-200%");
}

function toggleOrderDetailNameText(rowId){
    if($('#order-detail-name-extra-' + rowId).is(":hidden")){
           $('#order-detail-name-extra-' + rowId).show();
    } else {
           $('#order-detail-name-extra-' + rowId).hide();
    }
}


function displayOrderRows(jsonString) {
	orderDataArr = $.parseJSON(jsonString);
	var orderRowEnableTemplate = $('#order-row-enable-template').html();
	var orderRowDisableTemplate = $('#order-row-disable-template').html();
	var orderRowsHTML = '';
	
	$("#orderid").html(orderDataArr.orderid);
	$("#orderstatus").html(orderDataArr.statuslabel);
	$("#orderdate").html(orderDataArr.createdatdate);
	$("#ordername").html(orderDataArr.name);
	$("#orderemail").html(orderDataArr.email);
	$("#ordershippingdetails").html(orderDataArr.shippingdesc);
	if(orderDataArr.pickupschedule){
		$("#order-pickupschedule").show();
		$("#orderpickupschedule").html(orderDataArr.pickupschedule);
	}else{
		$("#order-pickupschedule").hide();
	}
		
	$.each(orderDataArr.products, function(index, obj) {
		var rowHTML = '';
		var qtytoship = parseInt(orderDataArr.products[index].qtytoship);
		
		if(qtytoship > 0){
			rowHTML = orderRowEnableTemplate;
		} else {
			rowHTML = orderRowDisableTemplate;
		}
		
		rowHTML = rowHTML.replace('[%code%]', orderDataArr.products[index].productid);
		rowHTML = rowHTML.replace('[%name%]', orderDataArr.products[index].productname);
		rowHTML = rowHTML.replace('[%sub-text%]', orderDataArr.products[index].productoptions);
		rowHTML = rowHTML.replace('[%price%]', orderDataArr.products[index].price);
		rowHTML = rowHTML.replace('[%ordered%]', orderDataArr.products[index].qtyinvoiced);
		rowHTML = rowHTML.replace('[%remain%]', orderDataArr.products[index].qtytoship);
		rowHTML = rowHTML.replace('[%delivered%]', 0);
		rowHTML = rowHTML.replace(/\[%id%\]/g, index);
		orderRowsHTML += rowHTML;
	});
	
	if(orderDataArr.status == 'complete'){
		$('#or-button-deliverall').addClass('button-deliver-all-disabled');
	}
	
	if((orderDataArr.status == 'closed') || (orderDataArr.status == 'cancelled')){		
		$('#save-order-btn').hide();
		$('#cancel-order-btn').hide();
	}
	
	$('#order-row-display').html(orderRowsHTML);
}

function displayComments(){
	$("#or-comment").val("");
	var commentTemplate = $('#comment-template').html();
	var commentRowsHTML = '';
	
	$.each(orderDataArr.comments, function(index, obj) {
		var rowHTML = commentTemplate;
		rowHTML = rowHTML.replace('[%commentdate%]', orderDataArr.comments[index].createdatdate);
		rowHTML = rowHTML.replace('[%commenttext%]', orderDataArr.comments[index].comment);
		commentRowsHTML += rowHTML;
	});
	$("#ordercomments").html(commentRowsHTML);
}

function toggleUpdateCountButton(rowId, changeValue) {
	if ( changeValue > 0 ) {
		$('#or-button-plus-'+rowId).toggleClass('order-count-button-plus order-count-button-plus-hover');
	} else {
		$('#or-button-minus-'+rowId).toggleClass('order-count-button-minus order-count-button-minus-hover');
	}
}

function updateCount(rowId, changeValue) {
	if ( changeValue > 0 ) {
		$('#or-button-plus-'+rowId).toggleClass('order-count-button-plus order-count-button-plus-hover');
	} else {
		$('#or-button-minus-'+rowId).toggleClass('order-count-button-minus order-count-button-minus-hover');
	}
	
	var qtyinvoiced = orderDataArr.products[rowId].qtyinvoiced;
	var qtytoship = orderDataArr.products[rowId].qtytoship;
	var deliver = parseInt($('#or-delivered-'+rowId).html());
	
	if( (deliver + changeValue >= 0) && (qtytoship - changeValue >= 0 ) ){
		deliver += changeValue;
		qtytoship -= changeValue;	
	}
	$('#or-remain-' + rowId).html(qtytoship);
	$('#or-delivered-' + rowId).html(deliver);
	orderDataArr.products[rowId].qtytoship = qtytoship;
	
	//setTimeout("toggleUpdateCountButton("+rowId+","+changeValue+")",200);
}

function toggleDeliverAllButton() {
	$('#or-button-deliverall').toggleClass('button-deliver-all button-deliver-all-hover');
}

function deliverAll(){
	$('#or-button-deliverall').toggleClass('button-deliver-all button-deliver-all-hover');
	var orderRowDiv = $('#order-row-display').find('.order-row');
	
	$.each(orderRowDiv, function(index, row){
		index = parseInt($(row).find('.order-detail-code').html());
		var qtytoship = orderDataArr.products[index].qtytoship;
		var deliver = parseInt($(row).find('#or-delivered-'+index).html());
		var total = qtytoship + deliver;
			
		if(qtytoship != 0){
			$(row).find('#or-remain-'+index).html(0);
			$(row).find('#or-delivered-'+index).html(total);
			orderDataArr.products[index].qtytoship = 0;
		}
	});
	//setTimeout("toggleDeliverAllButton()",200);
}


function search(){
		$('#mask').css("left","0px");
		$('#search-order-form').submit();
}


function saveOrders(){
	var orderRowDiv = $('#order-row-display').find('.order-row');
	$.each(orderRowDiv, function(index, row){
		index = parseInt($(row).find('.order-detail-code').html());
		var toShipQty = parseInt($(row).find('#or-delivered-'+index).html());
		orderDataArr.products[index].qtytoship = toShipQty;
	});
	orderDataArr.deliverycomment = $('#or-comment').val();
	orderDataArr.form_key = $('#form_key').val();
	if(window.location.pathname.indexOf('sid') >= 0){
		var pname = window.location.pathname;
		orderDataArr.sid = pname.substring((pname.indexOf('sid')+4), pname.length );
	}
	$('#mask-text').html('Saving');
	$('#mask').css("left","0px");
	$.ajax({
		url: $('#order-form').attr('action'),		
		type: "post",
		data: orderDataArr,
		success: function(response, textStatus, jqXHR){
			$('#order-data').val(JSON.stringify(response));
			renderContent();
			$('#mask').css("left","-200%");
		},
		failure: function(jqXHR, textStatus, errorThrown){			
		},
		complete:function(response){
		}
	});
}


function jqGet(url){
	$('#mask-text').html('Loading');
	$('#mask').css("left","0px");
	$.ajax({
		url: url,
		type: "get",
		data: "",
		success: function(response, textStatus, jqXHR){
			$('body').html(response);
			$('#mask').css("left","-200%");
			},
		failure: function(jqXHR, textStatus, errorThrown){
				
			},
		complete:function(response){
			
			}
		});	
}


function cancelOrder(urlString){
	if(confirm('Full amount will be refunded, even if the order is partially fulfilled. Would you like to proceed?')){
		$('#mask-text').html('Saving');
		$('#mask').css("left","0px");
		orderDataArr.deliverycomment = $('#or-comment').val();
		orderDataArr.form_key = $('#form_key').val();	
		$.ajax({
			url: urlString,		
			type: "post",
			data: orderDataArr,
			success: function(response, textStatus, jqXHR){
				$('#order-data').val(JSON.stringify(response));
				renderContent();
				$('#mask').css("left","-200%");
			},
			failure: function(jqXHR, textStatus, errorThrown){			
			},
			complete:function(response){
			}
		});
	}
}