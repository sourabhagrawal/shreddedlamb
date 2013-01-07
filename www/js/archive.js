var toggleArchiveList = function(listId, bulletId){
	var ulEl = document.getElementById(listId);
	var bulletEl = document.getElementById(bulletId);
	if(ulEl != undefined && bulletEl != undefined){
		if(ulEl.style.display != 'block'){
			ulEl.style.display = 'block';
			bulletEl.innerHTML = '&#9660;';
		}else{
			ulEl.style.display = 'none';
			bulletEl.innerHTML = '&#9654;';
		}
	}
}