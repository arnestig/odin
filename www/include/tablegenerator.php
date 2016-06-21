<?php

class TableGenerator
{
    private $columns = array();
    private $tableitems = array();

    public function __construct()
    {
    }

    public function addColumn( $column_name, $formatting, $items_bound )
    {
        $add_column = array(
                        'name' => $column_name,
                        'formatting' => $formatting,
                        'data_mapping' => $items_bound );
        array_push( $this->columns, $add_column );
    }


    /* 
    * Generic tablegenerator 
    */
    public function generateHTML()
    {
        $retval = '<table class="sortable"><tr>';
        foreach ( $this->columns as $column ) {
            $retval .= '<th>'.$column[ 'name' ].'</th>';
        }
        $retval .= '</tr><tr>';
        for ( $item_id = 0; $item_id < count( $this->tableitems ); $item_id++ ) {
            $retval .= '<tr>';
            foreach ( $this->columns as $column ) {
                $extracted_data = array();
                foreach ( $column[ 'data_mapping' ] as $reference ) {
                    $raw_data = $this->tableitems[ $item_id ][ $reference ];
                    array_push( $extracted_data, $raw_data );
                }
                $retval .= '<td>'.vsprintf( $column[ 'formatting' ], $extracted_data ).'</td>'; 
            }
            $retval .= '</tr>';
        }
        $retval .= '</table>';
        return $retval;
    }

    /* 
    * Host table generator for overview-page
    * TODO: rm class sortable?
    */
    public function generateHostHTML()
    {
        $retval = '<table class="table table-condensed nw-table sortable"><tr>';
        foreach ( $this->columns as $column ) {
            $retval .= '<th>'.$column[ 'name' ].'</th>';
        }
        $retval .= '</tr><tr>';
        for ( $item_id = 0; $item_id < count( $this->tableitems ); $item_id++ ) {
            $retval .= '<tr>';
            foreach ( $this->columns as $column ) {
                $extracted_data = array();
                foreach ( $column[ 'data_mapping' ] as $reference ) {
                    $raw_data = $this->tableitems[ $item_id ][ $reference ];
                    array_push( $extracted_data, $raw_data );
                }
                $retval .= '<td>'.vsprintf( $column[ 'formatting' ], $extracted_data ).'</td>'; 
            }
            $retval .= '</tr>';
        }
        $retval .= '</table>';
        return $retval;
    }



    public function setData( $multidimensional_array )
    {
        $this->tableitems = $multidimensional_array;
    }
}

?>