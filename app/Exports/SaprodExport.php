<?php

namespace App\Exports;

use App\Models\Saprod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SaprodExport implements FromCollection, WithHeadings
{
    private $codalte;

    // Constructor que acepta el parámetro del departamento
    public function __construct($codalte = null)
    {
        $this->codalte = $codalte;
    }


    public function headings(): array
    {
        return [
            'codprod',
            'descrip',
            'descrip2',
            'descrip3',
            'descrip4',
            'precio1',
            'precio2',
            'precio3',
            'costo',
            'referencia',
            'marca',
            'existencia',
            'instancia',
        ];
    }

    /*
        CASE
        WHEN destacado = 1 THEN '1'
        WHEN destacado = 0 THEN '0'
        END as destacado,*/

    public function collection()
    {
        $comercial  = session('comercialid') ;
        $codalte    = $this->codalte;

        return Saprod::selectRaw("codprod,descrip,descrip2,descrip3,descrip4,costod as precio1, costod2 as precio2, costod3 as precio3, preciod as costo,
        refere as referencia, marca, existen, codinst as instancia  ")
            ->where("comercial",$comercial)
            ->whereRaw(" codinst in (select codinst from sainsta where codalte like '$codalte%')")
            ->orderBy('marca')->get();

    }
}
