/**
 * Created by Colin Zhao
 * glcolin@hotmail.com
 * Date: 12/10/2014
 * Last Updated: 12/22/2014
 * Version: 1.02
 * @param an array of column indexes
 * @return void
 * by calling this plugin, the table cells will be merged intelligently.
 */
(function ( $ ) {
	
	//Global vars
	var arr = [];
	
	/**
	 *  Main
	 */
	$.fn.cellsmerge = function() {

		//Validate arguments:
		try{
			//Check if the element is a table:
			if(!this.is('table')) throw 'TableCellsMerger only accepts type TABLE.';
			//Check if argument list is empty:
			if(arguments.length == 0) throw 'TableCellsMerger requires al least 1 argument.';
			//Get table columns count:
			var cols = $(this).find("tbody tr:first td").length;
			//Push arguments into array:
			for (var i=0; i<arguments.length; i++){
				//Check if it is integer:
				if(arguments[i] % 1 !== 0) throw 'TableCellsMerger does not support non-integer arguments.';
				//Check if the argument is out of range:
				if(arguments[i] >= cols || arguments[i] < 0) throw 'TableCellsMerger error: argument is out of range of column count.';
				//Push:
				arr.push(arguments[i]);
			}
			
			//Add classes to cells:	
			this.find('tbody tr').each(function(){
				for (var i=0; i<arr.length; i++){
					var cell = new Cell($(this).find('td:eq('+arr[i]+')'));
					//Four possible cases:
					//1:Top Left Cell
					if(cell.left() == null && cell.up() == null){
						cell.initMarker();
					}
					//2:Left Column
					else if(cell.left() == null && cell.up() != null){
						if(cell.compareCell(cell.up())){
							cell.cloneMarker(cell.up());
						}else{
							cell.incrementMarker(cell.up());
						}
					}
					//3:Top Row
					else if(cell.left() != null && cell.up() == null){
						cell.extendMarker(cell.left());
					}
					//4:Other Cells
					else{			
						//The line below is a bit messy, but fixed the bug, leave it like that for now:
						if(!((cell.left().getMarker()+'-')==cell.up().getMarkerPrefix())){
							cell.extendMarker(cell.left());
						}else if(cell.compareCell(cell.up())){
							cell.cloneMarker(cell.up());
						}else{
							cell.incrementMarker(cell.up());
						}		
					}//END 4 possibilities;
				}
			});	
			
			//Merge cells:
			for (var i=0; i<arr.length; i++){
				//Get object of first row:
				var cell = new Cell(this.find('tbody tr:eq(0)').find('td:eq('+arr[i]+')'));
				//Loop through rows:
				do{
					var rows = $('td[data-marker='+cell.getMarker()+']');
					if(rows.length == 1){
						//do nothing;
						continue;
						//rows.css('background','blue');
					}else{
						rows.first().attr('rowspan', rows.length);
						rows.not(':first').addClass('marker-remove');
					}
				}while((cell = cell.down())!=null)
			}
				
			//Remove unnecessary cells:	
			$('.marker-remove').remove();

		}catch(err){
			console.error(err);
		}
		return this;
		
	};//END MAIN
	
	/**
	 * Class Cell
	 * Constructor
	 */
	function Cell(c){
		
		this.cell = c;

	}//END constructor

	/**
	 * temp
	 */
	Cell.prototype.write = function(x){
		this.cell.html(x);
	}
	
	/**
	 * Returns the Cell object of itself;
	 */
	Cell.prototype.node	= function(){
		return this.cell;
	}	
	
	/**
	 * Returns the Cell object in the left;
	 */
	Cell.prototype.left	= function(){
		var i = arr.indexOf(this.cell.index())-1;
		if(i >= 0){
			return new Cell(this.cell.parent().find('td:eq('+arr[i]+')'));
		}else{
			return null;
		}
	}	
	
	/**
	 *  Returns the Cell object one row up;
	 */
	Cell.prototype.up = function(){
		var node = this.cell.parent().prev().find('td:eq('+this.cell.index()+')');
		if(node.length != 0 && node.is('td')){
			return new Cell(node);
		}else{
			return null;
		}
	}	
	
	/**
	 *  Returns the Cell object one row down;
	 */
	Cell.prototype.down = function(){
		var node = this.cell.parent().next().find('td:eq('+this.cell.index()+')');
		if(node.length != 0 && node.is('td')){
			return new Cell(node);
		}else{
			return null;
		}
	}

	/**
	 * Set a marker; 
	 * * @param String x
	 */
	Cell.prototype.setMarker = function(x){
		this.cell.attr('data-marker',x);
	}	
	
	/**
	 * Get a marker; 
	 */
	Cell.prototype.getMarker = function(){
		if(this.cell.data('marker')!=null){
			return this.cell.data('marker');
		}else{
			return '';
		}
	}

	/**
	 *  Remove a marker;
	 */
	Cell.prototype.removeMarker = function(){
		this.cell.removeAttr('data-marker');
	}	

	/**
	 *  Initialize a marker;
	 */
	Cell.prototype.initMarker = function(){
		this.setMarker('marker-0');
	}	
	
	/**
	 *  Get marker prefix;
	 */
	Cell.prototype.getMarkerPrefix = function(){
		return this.getMarker().replace(/[^-]+$/, "");
	}
	
	/**
	 *  Get marker head;
	 */
	Cell.prototype.getMarkerHead = function(){
		return this.getMarker().replace(/^.+-/, "");
	}
	
	/**
	 *  Extend a marker;
	 * * @param {Object} m
	 */
	Cell.prototype.extendMarker = function(m){
		this.setMarker(m.getMarker()+'-0');
	}
	
	/**
	 *  Clone a marker;
	 * * @param {Object} m
	 */
	Cell.prototype.cloneMarker = function(m){
		this.setMarker(m.getMarker());
	}
	
	/**
	 *  Increment a marker;
	 * * @param {Object} m
	 */
	Cell.prototype.incrementMarker = function(m){
		this.setMarker(m.getMarkerPrefix()+(parseInt(m.getMarkerHead())+1));
	}
	
	/**
	 *  Compare marker prefix with another cell;(Boolean)
	 * * @param {Object} m
	 */
	Cell.prototype.compareMarkerPrefix = function(m){
		return (this.getMarkerPrefix() == m.getMarkerPrefix());
	}
	
	/**
	 * Compare the the trimmed html inside cells 
 	 * @param {Object} m
	 */
	Cell.prototype.compareCell = function(m){
		return (this.cell.html().trim() == m.cell.html().trim());
	}
	
}( jQuery ));