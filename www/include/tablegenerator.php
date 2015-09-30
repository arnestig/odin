<?php

class TableGenerator
{
    private $columns = array();
    private $tableitems = array();

    public function __construct( $array_of_columns )
    {
        $this->columns = $array_of_columns;
    }

    public function addColumn( $column )
    {
        array_push( $this->columns, $column );
    }

    public function setData( $multidimensional_array )
    {
        $this->tableitems = $multidimensional_array;
    }

    public function generateHTML()
    {
        $retdata = '<table border=1px><tr><th>'.implode( '</th><th>', $this->columns ).'</th></tr>';
        foreach ( $this->tableitems as $item ) {
            $retdata .= '<tr><td>'.implode( '</td><td>', $item ).'</td></tr>';
        }
        $retdata .= '</table>';
        return $retdata;
    }
}

?>
