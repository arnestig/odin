<?php

include_once( "include/odinhtml.php" );

class TableGenerator
{
    private $columns = array();
    private $tableitems = array();
    private $htmldoc;
    public function __construct($doc)
    {
        $this->htmldoc = $doc; 
        /* or one could create a new doc and 
           use import instead of append
           on the calling side */
    }

    public function addColumn( $column_name, $formatting, $items_bound )
    {
        $add_column = array(
                        'name' => $column_name,
                        'formatting' => $formatting,
                        'data_mapping' => $items_bound );
        array_push( $this->columns, $add_column );
    }

    public function generateHTML()
    {
        //$htmldoc = new OdinHTML();

        $tableNode = $this->htmldoc->createTable('usrtbl','usrtbl','sortable');

        $rowNode = $this->htmldoc->createElement('tr');
        foreach ( $this->columns as $column ) {
            $colNode = $this->htmldoc->createElement('th', $column[ 'name' ]);
            $rowNode->appendChild($colNode);
        }
        $tableNode->appendChild($rowNode);
        
        for ( $item_id = 0; $item_id < count( $this->tableitems ); $item_id++ ) {
            $rowNode = $this->htmldoc->createElement('tr');
            foreach ( $this->columns as $column ) {
                $extracted_data = array();
                foreach ( $column[ 'data_mapping' ] as $reference ) {
                    $raw_data = $this->tableitems[ $item_id ][ $reference ];
                    array_push( $extracted_data, $raw_data );
                }
                // This
                $innerHtml = vsprintf( $column[ 'formatting' ], $extracted_data );
                $fragment = $this->htmldoc->createDocumentFragment();
                $fragment->appendXML('<td>'. $innerHtml . '</td>');
                $rowNode->appendChild($fragment);
                // Or This
                //$colNode = $this->htmldoc->createElement('td', $innerHtml);
                //$rowNode->appendChild($colNode);
            }
            $tableNode->appendChild($rowNode);
        }
        return $tableNode;
    }

    public function setData( $multidimensional_array )
    {
        $this->tableitems = $multidimensional_array;
    }
}

?>
