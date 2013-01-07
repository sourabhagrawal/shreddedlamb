String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
};

String.prototype.trim = function(){
	a = this.replace(/^\s+/,'');
	return a.replace(/\s+$/,'');
};

var AutoComplete = function(id, name, items, prefilledValue){
	this.listItems = items;

	var ref = this;
	
	this.onKeyUp = function(event){
		var target = event.target;
		var key = event.which;
		var value = target.value;
		var query = value.substr(value.lastIndexOf(',') + 1);
		ref.createList(query.trim());
	};
	
	this.onLiClick = function(event){
		var value = this.text;
		var currValue = ref.input.value;
		ref.input.value = currValue.substr(0, currValue.lastIndexOf(',') + 1) + value + ",";
		
		var listWrapper = document.getElementById(ref.listId);
		listWrapper.innerHTML = "";
		ref.input.focus();
	};

	this.createList = function(query){
		if(query == undefined || query.trim() == ""){
			var listWrapper = document.getElementById(this.listId);
			listWrapper.innerHTML = "";
			return;
		}
		
		var length = this.listItems.length;
		var filtered = [];
		
		var currValue = ref.input.value;
		currValue = currValue.substr(0, currValue.lastIndexOf(',') + 1);
		var selected = currValue.split(',');
		
		for(var i = 0; i < length; i++){
			var item = this.listItems[i];
			if(item.startsWith(query) && selected.indexOf(item) == -1){
				filtered.push(item);
			}
		}
		var key = Math.floor(Math.random()*1001);
		var html = "<ul class='autocomplete-list' id='autocomplete-list-" + key + "'>";
		for(var i = 0; i < filtered.length; i++){
			var liId = 'autocomplete-item-' + key + '-' + i;
			html += "<li id='" + liId + "'>" + filtered[i].replace(query, "<b>" + query + "</b>") + "</li>";
		}
		html += "</ul>";
		var listWrapper = document.getElementById(this.listId);
		listWrapper.innerHTML = html;
		
		for(var i = 0; i < filtered.length; i++){
			var liId = 'autocomplete-item-' + key + '-' + i;
			var li = document.getElementById(liId);
			li.text = filtered[i];
			li.addEventListener('click', this.onLiClick, false);
		}
	};

	this.wrapper = document.getElementById(id);
	if(this.wrapper){
		var key = Math.floor(Math.random()*1001);
		this.inputId = 'autocomplete-input-' + key;
		this.listId = 'autocomplete-list-' + key;
		this.wrapper.innerHTML = "<input autocomplete='off' id='" + this.inputId + "' name=" + name + " type='text'/><div id='" + this.listId + "'></div>";

		this.input = document.getElementById(this.inputId);
		if(this.input){
			if(prefilledValue != undefined)
				this.input.value = prefilledValue;
			this.input.addEventListener('keyup', this.onKeyUp, false);
		}
	}
	
	return this.inputId;
};