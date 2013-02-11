$(function()
{
	$('#popup .close').live('click', function(){
		hidePopup();
	});
	$('#backgroundPopup').live('click', function(){
		hidePopup();
	});
	$('#popup .close').live('mouseover', function(){
		$(this).addClass('over');
	});
	$('#popup .close').live('mouseleave', function(){
		$(this).removeClass('over');
	});
});

function getCenterPosition(targetWidth, targetHeight) 
{     
	var win = $(window); 
	var target = 
	{  
		top: Math.round((win.height()-targetHeight)/2)+win.scrollTop(),
		left: Math.round((win.width()-targetWidth)/2)+win.scrollLeft()     
	}     
	
	if (target.top < 0) 
		target.top = 0;     
	
	if (target.left < 0) 
		target.left = 0;
	
	return target;
}

function centerPopup(id, moveup)
{	
	var popupWidth = $("#"+id).width();
	var popupHeight = $("#"+id).height();

	var target = getCenterPosition(popupWidth, popupHeight);
	
	var top = target.top;
	if (typeof moveup != 'undefined')
	{
		top -= moveup;
		// No point in putting above the viewport
		if (top < 0)
		{
			top = 0;
		}
	}

	//centering
	$("#"+id).css({
		"position": "fixed",
		"top": top,
		"left": target.left
	});
}

function setPopupTitle(title)
{
	$("#popup .title").html(title);
}
function setPopupContent(content)
{
	$("#popup .content").html(content);
}
function displayPopup()
{
	centerPopup('popup');
	$("#backgroundPopup").fadeIn(500);
	$("#popup").fadeIn(500);
}
function hidePopup()
{
	var delay = 100;
	$("#popup").fadeOut(delay);
	$("#backgroundPopup").fadeOut(delay);
	$("#popup .title").html('');
	$("#popup .content").html('');
}