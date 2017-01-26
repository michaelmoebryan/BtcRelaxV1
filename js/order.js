function checkState()
{
$(document.getElementById('btnOrderRefresh')).enabled = false;
var xmlhttp = new XMLHttpRequest();
var baseUrl = this.location.protocol + "//" + this.location.hostname + (location.port ? ':'+location.port: '');
var vOrderState = $(document.getElementsByName('orderStateId')).val();
var vStateHash = $(document.getElementsByName('stateHash')).val(); 
var vOrderId = $(document.getElementsByName('orderId')).val();

if (vOrderState != undefined)
{
    var url = baseUrl + "/ganja/checkOrderState.php?idOrder=" + vOrderId 
    + "&stateHash=" + vStateHash ;

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            isNeedRefresh(xmlhttp.responseText);
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    
}
};

function selectBookmarkId(idbookmark)
{
    $(document.getElementById('IdSelectedBookMark')).val(idbookmark);
    $.post(document.URL);
}

function setPointCatched(idbookmark, idOrder)
{
	var xmlhttp = new XMLHttpRequest();
	var baseUrl = this.location.protocol + '//' + this.location.hostname + (location.port ? ':'+location.port: '');
	var url = baseUrl + "/api/json.php?method=setPointCatched&bookmarkId=" + idbookmark + '&orderId=' + idOrder;
		
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			checkState();
		}
	}
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
}

function isNeedRefresh(response,targetObject){
	$(document.getElementById('btnOrderRefresh')).enabled = true;
	var arr = JSON.parse(response);    
	if (arr != undefined)
	{
		var out = arr[0].isStateActual;
		if (out != 1)
		{
			location.reload(true);            
		}  
	};    
};

function exitFromOrder()
{
	$.ajax({
	  method: "POST",
	  url: "killSession.php"
//      data: { name: "John", location: "Boston" }
	})
	  .done(function( ) {
		window.location.assign(document.baseURI);
        location.reload();
	  })
		
};

$(window).on('load', function(){
	   var vRefTimer = setInterval(function(){checkState();}, 15000);
})