<?php

namespace Crater\Console\Commands;

use Afip;
use Carbon\Carbon;
use Illuminate\Console\Command;

class feAfip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'afip:fetest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'testeo FE contra AFIP Homologacion';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $options = [
            'CUIT' => 20263339615,
            'production' => false,
            'passphrase' => "xxxxx",
            'exceptions' => false,
            'cert' => "homo_cert.crt",
            'key' => "privada",
        ];

        $afip_fe = new Afip($options);

        $last_voucher = intval($afip_fe->ElectronicBilling->GetLastVoucher(6,6)) + 1; //Devuelve el número del último comprobante creado para el punto de venta 1 y el tipo de comprobante 6 (Factura B)
        dump($last_voucher);

        //$last_authorized = $afip_fe->ElectronicBilling->GetLastVoucher(1,6);
        //dump($last_authorized);

        //GetVoucherInfo($number, $sales_point, $type)
        //dd($afip_fe->ElectronicBilling->GetVoucherInfo($last_voucher-1, 6, 6));

        $fecha = Carbon::now()->format('Ymd');
        dump($fecha);
        
        //$voucher_types = $afip_fe->ElectronicBilling->GetVoucherTypes();
        // 18 => {#2549
        //     +"Id": 11
        //     +"Desc": "Factura C"
        //     +"FchDesde": "20110330"
        //     +"FchHasta": "NULL"
        //   }
    

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> 6,  // Punto de venta
            'CbteTipo' 	=> 6,  // Tipo de comprobante (Factura B)(ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> 0,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' 	=> $last_voucher,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' 	=> $last_voucher,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> $fecha, // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> 121, // Importe total del comprobante
            'ImpTotConc' 	=> 0,   // Importe neto no gravado
            'ImpNeto' 	=> 100, // Importe neto gravado
            'ImpOpEx' 	=> 0,   // Importe exento de IVA
            'ImpIVA' 	=> 21,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
                array(
                    'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp' 	=> 100, // Base imponible
                    'Importe' 	=> 21 // Importe 
                )
            ), 
        );
        
        $res = $afip_fe->ElectronicBilling->CreateVoucher($data);

        dump($afip_fe);

    }
}
