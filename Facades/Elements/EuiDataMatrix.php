<?php
namespace exface\JEasyUIFacade\Facades\Elements;

use exface\Core\Widgets\DataColumnTransposed;
use exface\Core\Widgets\DataMatrix;

/**
 *
 * @method DataMatrix getWidget()
 *        
 * @author aka
 *        
 */
class EuiDataMatrix extends EuiDataTable
{

    private $label_values = array();

    protected function init()
    {
        parent::init();
        $this->setElementType('datagrid');
        $this->buildJsTransposer();
        $this->addOnLoadSuccess($this->buildJsCellMerger());
    }

    protected function buildJsTransposer()
    {
        $visible_cols = array();
        $data_cols = array();
        $data_cols_totlas = array();
        $label_cols = array();
        foreach ($this->getWidget()->getColumns() as $col) {
            if ($col instanceof DataColumnTransposed) {
                $data_cols[] = $col->getDataColumnName();
                $label_cols[$col->getLabelAttributeAlias()][] = $col->getDataColumnName();
                if ($col->getFooter()) {
                    $data_cols_totlas[$col->getDataColumnName()] = $col->getFooter();
                }
            } elseif (! $col->isHidden()) {
                $visible_cols[] = $col->getDataColumnName();
            }
        }
        $visible_cols = "'" . implode("','", $visible_cols) . "'";
        $data_cols = "'" . implode("','", $data_cols) . "'";
        $label_cols = json_encode($label_cols);
        $data_cols_totlas = json_encode($data_cols_totlas);
        
        $transpose_js = <<<JS

$("#{$this->getId()}").data("_skipNextLoad", true);

var dataCols = [ {$data_cols} ];
var dataColsTotals = {$data_cols_totlas};
var labelCols = {$label_cols};
var rows = data.rows;
var cols = $(this).datagrid('options').columns;
var colsNew = [];
var colsTransposed = {};
var colsTranspCount = 0;
for (var i=0; i<cols.length; i++){
	var newColRow = [];
	for (var j=0; j<cols[i].length; j++){
		var fld = cols[i][j].field;
		if (dataCols.indexOf(fld) > -1){
			data.transposed = 0;
			colsTransposed[fld] = {
				column: cols[i][j],
				subRowIndex: colsTranspCount++,
				colIndex: j
			};
		} else if (labelCols[fld] != undefined) {
			// Add a subtitle column to show a caption for each subrow if there are multiple
			if (dataCols.length > 1){
				var newCol = {};
				newCol.field = '_subRowIndex';
				newCol.title = '';
				newCol.align = 'right';
				newCol.sortable = false;
				newCol.hidden = true;
				newColRow.push(newCol);

				var newCol = $.extend(true, {}, cols[i][j]);
				newCol.field = fld+'_subtitle';
				newCol.title = '';
				newCol.align = 'right';
				newCol.sortable = false;
				newColRow.push(newCol);
			}
			// Create a column for each value if the label column
			var labels = [];
			for (var l=0; l<rows.length; l++){
				if (labels.indexOf(rows[l][fld]) == -1){
					labels.push(rows[l][fld]);
				}
			}
			for (var l=0; l<labels.length; l++){
				var newCol = $.extend(true, {}, cols[i][j]);
				newCol.field = labels[l];
				newCol.title = '<span title="'+$(newCol.title).text()+' '+labels[l]+'">'+labels[l]+'</title>';
				newCol._transposedFields = labelCols[fld];
				// No header sorting if multiple sublines (not clear, what to sort!)
				if (dataCols.length > 1){
					newCol.sortable = false;
				}
				newColRow.push(newCol);
			}
			// Create a totals column if there are totals
			if (dataColsTotals !== {}){
				var totals = [];
				for (var t in dataColsTotals){
					var tfunc = dataColsTotals[t];
					if (totals.indexOf(tfunc) == -1){
						var newCol = $.extend(true, {}, cols[i][j]);
						newCol.field = fld+'_'+tfunc;
						newCol.title = tfunc;
						newCol.align = 'right';
						if (dataCols.length > 1){
							newCol.sortable = false;
						}
						newColRow.push(newCol);
						totals.push(tfunc);
					}
				}
			}
		} else {
			newColRow.push(cols[i][j]);
		}
	}
	for (var i in colsTransposed){
		if (colsTransposed[i].column.editor != undefined){
			for (var j=0; j<newColRow.length; j++){
				if (newColRow[j]._transposedFields != undefined && newColRow[j]._transposedFields.indexOf(i) > -1){
					newColRow[j].editor = colsTransposed[i].column.editor;
				}
			}
		}
	}
	colsNew.push(newColRow);
}

if (data.transposed === 0){
	var newRows = [];
	var newRowsObj = {};
	var visibleCols = [ {$visible_cols} ];
	for (var i=0; i<rows.length; i++){
		var newRowId = '';
		var newRow = {};
		var newColVals = {};
		var newColId = '';
		for (var fld in rows[i]){
			var val = rows[i][fld];
			if (labelCols[fld] != undefined){
				newColId = val;
				newColGroup = fld;
			} else if (dataCols.indexOf(fld) > -1){
				newColVals[fld] = val; 
			} else if (visibleCols.indexOf(fld) > -1) {
				newRowId += val;
				newRow[fld] = val;
			}

			// TODO save UID and other system attributes to some invisible data structure 
		}

		var subRowCounter = 0;
		for (var fld in newColVals){
			if (newRowsObj[newRowId+fld] == undefined){
				newRowsObj[newRowId+fld] = $.extend(true, {}, newRow);
				newRowsObj[newRowId+fld]['_subRowIndex'] = subRowCounter++;
			}
			newRowsObj[newRowId+fld][newColId] = newColVals[fld];
			newRowsObj[newRowId+fld][newColGroup+'_subtitle'] = '<i>'+colsTransposed[fld].column.title+'</i>';
			if (dataColsTotals[fld] != undefined){
				var newVal = parseFloat(newColVals[fld]);
				var oldVal = newRowsObj[newRowId+fld][newColGroup+'_'+dataColsTotals[fld]];
				oldVal = oldVal ? oldVal : 0;
				switch (dataColsTotals[fld]){
					case 'SUM':
						newRowsObj[newRowId+fld][newColGroup+'_'+dataColsTotals[fld]] = oldVal + newVal; 
						break;
					case 'MAX':
						newRowsObj[newRowId+fld][newColGroup+'_'+dataColsTotals[fld]] = oldVal < newVal ? newVal : oldVal; 
						break;
					case 'MIN':
						newRowsObj[newRowId+fld][newColGroup+'_'+dataColsTotals[fld]] = oldVal > newVal ? newVal : oldVal; 
						break;
					case 'COUNT':
						newRowsObj[newRowId+fld][newColGroup+'_'+dataColsTotals[fld]] = oldVal + 1; 
						break;
					// TODO add more totals
				}
			}
		}
	}
	for (var i in newRowsObj){
		newRows.push(newRowsObj[i]);
	}
	
	data.rows = newRows;
	data.transposed = 1;
	$(this).datagrid({columns: colsNew});
}
	

return data;
				
JS;
        $this->addLoadFilterScript($transpose_js);
    }

    protected function buildJsCellMerger()
    {
        $fields_to_merge = array();
        foreach ($this->getWidget()->getColumnsRegular() as $col) {
            $fields_to_merge[] = $col->getDataColumnName();
        }
        $fields_to_merge = json_encode($fields_to_merge);
        $rowspan = count($this->getWidget()->getColumnsTransposed());
        
        $output = <<<JS

			var fields = {$fields_to_merge};
			for (var i=0; i<fields.length; i++){
	            for(var j=0; j<$(this).datagrid('getRows').length; j++){
	                $(this).datagrid('mergeCells',{
	                    index: j,
	                    field: fields[i],
	                    rowspan: {$rowspan}
	                });
					j = j+{$rowspan}-1;
	            }
			}

JS;
        return $output;
    }

    public function buildJsInitOptionsHead()
    {
        $options = parent::buildJsInitOptionsHead();
        
        // If we have multiple transposed columns, we must sort on the client to make sure, the transposed columns
        // are attached to their spanning columns and stay in exactly the same order. So we add a custom sorter to
        // the event fired when a user is about to sort a column.
        // NOTE: we can't switch to sorting on the client generally, because this won't work if the initial sorting
        // is done over a transposed column or a label column. And sorting over label column is what you mostly will
        // need to do to ensure a meaningfull order of the transposed values.
        if (count($this->getWidget()->getColumnsTransposed()) > 1) {
            $options .= <<<JS
				, onBeforeSortColumn: function(sort, order){
					var remoteSortSetting = $(this).datagrid('options').remoteSort;
					$(this).datagrid('options').remoteSort = false;
					if (!$(this).datagrid('options')._customSort){
						$(this).datagrid('options')._customSort = true;
						$(this).datagrid('sort', {
							sortName: sort+',_subRowIndex',
							sortOrder: order+',asc'
						});
						$(this).datagrid('options')._customSort = false;
						return false;
					}
					$(this).datagrid('options').remoteSort = remoteSortSetting;
				}
JS;
        }
        return $options;
    }

    public function buildJsEditModeEnabler()
    {
        $editable_transposed_cols = array();
        foreach ($this->getWidget()->getColumnsTransposed() as $pos => $col) {
            if ($col->isEditable()) {
                $editable_transposed_cols[] = $pos;
            }
        }
        $editable_transposed_cols = json_encode($editable_transposed_cols);
        return <<<JS
					var rows = $(this).{$this->getElementType()}("getRows");
					for (var i=0; i<rows.length; i++){
						if ({$editable_transposed_cols}.indexOf(rows[i]._subRowIndex) > -1){
							$(this).{$this->getElementType()}("beginEdit", i);
						}
					}
JS;
    }
}
?>