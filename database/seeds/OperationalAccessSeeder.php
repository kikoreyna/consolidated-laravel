<?php

namespace Database\Seeders;

use App\Bodega;
use App\Cliente;
use App\Cobertura;
use App\Codigor;
use App\Conductor;
use App\Consolidado;
use App\ConsolidadoMovimiento;
use App\Destinatario;
use App\Entrada;
use App\EntradaMovimiento;
use App\Medicion;
use App\Oficina;
use App\Reempacador;
use App\Remitente;
use App\Transportadora;
use App\User;
use App\Vehiculo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OperationalAccessSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $protectedEmail = env('PROTECTED_SUPERADMIN_EMAIL', 'freyna@lapotosinaexpress.com');
            $seedPassword = Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'password'));
            $startDate = Carbon::now()->subMonth()->startOfDay();
            $endDate = Carbon::now();

            $bodegas = $this->seedBodegas();
            $clientes = $this->seedClientes();
            $usuarios = $this->seedUsers($bodegas, $clientes, $seedPassword, $protectedEmail);
            $catalogos = $this->seedCatalogos($bodegas);
            $this->seedCoberturas($clientes, $bodegas, $catalogos['transportadoras'], $catalogos['oficinas'], $startDate, $endDate);
            $consolidados = $this->seedConsolidados($clientes, $usuarios['supervisor'], $startDate, $endDate);
            $entradas = $this->seedEntradas($clientes, $bodegas, $catalogos, $consolidados, $usuarios, $startDate, $endDate);
            $this->seedConsolidadoMovements($consolidados, $usuarios['supervisor'], $startDate, $endDate);
            $this->seedEntryMovementsAndObservations($entradas, $usuarios, $startDate, $endDate);
        });
    }

    private function seedBodegas()
    {
        return [
            'usa' => Bodega::updateOrCreate(
                ['codigo' => 'USA'],
                [
                    'nombre' => 'Bodega USA',
                    'descripcion' => 'Recepción y control de guías en Estados Unidos.',
                    'pais' => 'USA',
                    'activa' => true,
                    'control_usa' => 'pesar_medir',
                ]
            ),
            'mexico' => Bodega::updateOrCreate(
                ['codigo' => 'MEXICO'],
                [
                    'nombre' => 'Bodega México',
                    'descripcion' => 'Recepción y control de guías en México.',
                    'pais' => 'México',
                    'activa' => true,
                    'control_usa' => 'solo_recibido',
                ]
            ),
        ];
    }

    private function seedClientes()
    {
        $clientes = [];
        $datos = [
            ['alias' => 'DEMO', 'nombre' => 'Cliente Demo', 'ciudad' => 'Ciudad de México', 'estado' => 'CDMX'],
            ['alias' => 'LPE', 'nombre' => 'Lapotosina Express', 'ciudad' => 'Monterrey', 'estado' => 'Nuevo León'],
            ['alias' => 'FDV', 'nombre' => 'Frutas del Valle', 'ciudad' => 'Puebla', 'estado' => 'Puebla'],
            ['alias' => 'TEC', 'nombre' => 'Tecnología del Centro', 'ciudad' => 'Querétaro', 'estado' => 'Querétaro'],
            ['alias' => 'HOG', 'nombre' => 'Hogar y Oficina', 'ciudad' => 'Guadalajara', 'estado' => 'Jalisco'],
            ['alias' => 'IND', 'nombre' => 'Industriales del Norte', 'ciudad' => 'Tijuana', 'estado' => 'Baja California'],
        ];

        foreach ($datos as $indice => $dato) {
            $clientes[$dato['alias']] = Cliente::updateOrCreate(
                ['alias' => $dato['alias']],
                [
                    'nombre' => $dato['nombre'],
                    'contacto' => 'Contacto ' . $dato['nombre'],
                    'telefono' => '555-01' . str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT),
                    'correo_electronico' => strtolower($dato['alias']) . '@demo.example',
                    'direccion' => 'Avenida Principal ' . ($indice + 10),
                    'ciudad' => $dato['ciudad'],
                    'estado' => $dato['estado'],
                    'pais' => 'México',
                    'notas' => 'Cliente de demostración con datos del último mes.',
                ]
            );
        }

        return $clientes;
    }

    private function seedUsers($bodegas, $clientes, $password, $protectedEmail)
    {
        $usuarios = [];
        $usuarios['usa'] = $this->upsertUser('bodega-usa@example.com', 'Operador Bodega USA', 'bodega_usa', $password, $protectedEmail);
        $usuarios['usa']->bodegas()->syncWithoutDetaching([$bodegas['usa']->id]);

        $usuarios['mexico'] = $this->upsertUser('bodega-mexico@example.com', 'Operador Bodega México', 'bodega_mexico', $password, $protectedEmail);
        $usuarios['mexico']->bodegas()->syncWithoutDetaching([$bodegas['mexico']->id]);

        $usuarios['supervisor'] = $this->upsertUser('supervisor@example.com', 'Supervisor de Operaciones', 'supervisor', $password, $protectedEmail);
        $usuarios['supervisor']->bodegas()->syncWithoutDetaching([$bodegas['usa']->id, $bodegas['mexico']->id]);

        $usuarios['documentador'] = $this->upsertUser('documentador@example.com', 'Documentador de Guías', 'documentador', $password, $protectedEmail);
        $usuarios['documentador']->bodegas()->syncWithoutDetaching([$bodegas['usa']->id, $bodegas['mexico']->id]);

        $usuarios['cliente'] = $this->upsertUser('cliente-demo@example.com', 'Usuario Cliente Demo', 'cliente', $password, $protectedEmail);
        $usuarios['cliente']->clientes()->syncWithoutDetaching([$clientes['DEMO']->id]);

        return $usuarios;
    }

    private function seedCatalogos($bodegas)
    {
        $transportadoras = [];
        foreach ([
            ['nombre' => 'Transportes del Centro', 'web' => 'https://centro.example'],
            ['nombre' => 'Paquetería Frontera', 'web' => 'https://frontera.example'],
            ['nombre' => 'Logística Nacional', 'web' => 'https://nacional.example'],
        ] as $dato) {
            $transportadoras[$dato['nombre']] = Transportadora::updateOrCreate(
                ['nombre' => $dato['nombre']],
                [
                    'web' => $dato['web'],
                    'telefono' => '555-0200',
                    'notas' => 'Transportadora disponible para pruebas de entrega.',
                ]
            );
        }

        $oficinas = [];
        foreach ($transportadoras as $transportadora) {
            foreach (['Sucursal Centro', 'Sucursal Aeropuerto'] as $nombre) {
                $oficinas[$transportadora->id . '-' . $nombre] = Oficina::updateOrCreate(
                    ['transportadora_id' => $transportadora->id, 'nombre' => $nombre],
                    [
                        'contacto' => 'Mesa de atención',
                        'telefono' => '555-02' . $transportadora->id,
                        'observaciones' => 'Oficina activa para datos de demostración.',
                        'activa' => true,
                    ]
                );
            }
        }

        $conductores = [];
        foreach (['Carlos Ramírez', 'María López', 'Jorge Santos', 'Ana Torres', 'Luis Mendoza', 'Patricia Cruz'] as $nombre) {
            $conductores[$nombre] = Conductor::firstOrCreate(['nombre' => $nombre]);
        }

        $vehiculos = [];
        foreach (['Unidad Demo 01', 'Unidad Demo 02', 'Unidad Demo 03', 'Unidad Demo 04', 'Unidad Demo 05', 'Unidad Demo 06'] as $indice => $alias) {
            $vehiculos[$alias] = Vehiculo::updateOrCreate(
                ['alias' => $alias],
                ['descripcion' => 'Vehículo de demostración número ' . ($indice + 1)]
            );
        }

        $reempacadores = [];
        foreach ([['nombre' => 'Reempaque Central', 'clave' => 'RC-01'], ['nombre' => 'Reempaque Norte', 'clave' => 'RN-01'], ['nombre' => 'Reempaque Sur', 'clave' => 'RS-01']] as $dato) {
            $reempacadores[$dato['clave']] = Reempacador::updateOrCreate(['clave' => $dato['clave']], ['nombre' => $dato['nombre']]);
        }

        $codigos = [];
        foreach ([['nombre' => 'R1', 'descripcion' => 'Producto nacional'], ['nombre' => 'R2', 'descripcion' => 'Producto importado'], ['nombre' => 'FRAGIL', 'descripcion' => 'Manejo especial'], ['nombre' => 'REF', 'descripcion' => 'Producto refrigerado']] as $dato) {
            $codigos[$dato['nombre']] = Codigor::updateOrCreate(['nombre' => $dato['nombre']], ['descripcion' => $dato['descripcion']]);
        }

        $mediciones = [];
        foreach (['Inicial', 'Extranjero', 'Nacional', 'Revisión de peso', 'Control dimensional'] as $nombre) {
            $mediciones[$nombre] = Medicion::firstOrCreate(['nombre' => $nombre]);
        }
        foreach ($bodegas as $bodega) {
            foreach ($mediciones as $medicion) {
                DB::table('bodega_mediciones')->insertOrIgnore([
                    'bodega_id' => $bodega->id,
                    'medicion_id' => $medicion->id,
                ]);
            }
        }

        return compact('transportadoras', 'oficinas', 'conductores', 'vehiculos', 'reempacadores', 'codigos', 'mediciones');
    }

    private function seedCoberturas($clientes, $bodegas, $transportadoras, $oficinas, $startDate, $endDate)
    {
        $transportadoraList = array_values($transportadoras);
        $oficinaList = array_values($oficinas);
        foreach (array_values($clientes) as $indice => $cliente) {
            $transportadora = $transportadoraList[$indice % count($transportadoraList)];
            $oficina = collect($oficinaList)->first(function ($item) use ($transportadora) {
                return (int) $item->transportadora_id === (int) $transportadora->id;
            });

            Cobertura::updateOrCreate(
                ['cliente_id' => $cliente->id, 'modalidad_entrega' => 'domicilio', 'ciudad' => $cliente->ciudad],
                [
                    'bodega_id' => $bodegas['mexico']->id,
                    'transportadora_id' => $transportadora->id,
                    'vigencia_desde' => $startDate->toDateString(),
                    'vigencia_hasta' => $endDate->copy()->addMonths(3)->toDateString(),
                    'activa' => true,
                    'estado' => $cliente->estado,
                    'observaciones' => 'Cobertura domiciliaria activa.',
                ]
            );
            Cobertura::updateOrCreate(
                ['cliente_id' => $cliente->id, 'modalidad_entrega' => 'ocurre', 'oficina_id' => $oficina->id],
                [
                    'transportadora_id' => $transportadora->id,
                    'bodega_id' => $bodegas['mexico']->id,
                    'vigencia_desde' => $startDate->toDateString(),
                    'vigencia_hasta' => $endDate->copy()->addMonths(3)->toDateString(),
                    'activa' => true,
                    'ciudad' => $cliente->ciudad,
                    'estado' => $cliente->estado,
                    'observaciones' => 'Cobertura ocurre activa.',
                ]
            );
        }
    }

    private function seedConsolidados($clientes, $usuario, $startDate, $endDate)
    {
        $consolidados = [];
        $clienteList = array_values($clientes);
        for ($indice = 1; $indice <= 12; $indice++) {
            $cliente = $clienteList[($indice - 1) % count($clienteList)];
            $createdAt = $this->dateInRange($startDate, $endDate, $indice * 2);
            $cerrado = $indice % 5 === 0;
            $consolidado = Consolidado::updateOrCreate(
                ['numero' => 'DEMO-CON-' . str_pad((string) $indice, 3, '0', STR_PAD_LEFT)],
                [
                    'cliente_id' => $cliente->id,
                    'palets' => 4 + ($indice % 8),
                    'cliente_alias_numero' => $indice % 3 === 0,
                    'notificacion' => $createdAt->copy()->addHours(4),
                    'cerrado' => $cerrado,
                    'cerrado_at' => $cerrado ? $createdAt->copy()->addDays(2) : null,
                    'cerrado_por' => $cerrado ? $usuario->id : null,
                ]
            );
            $this->setTimestamps($consolidado, $createdAt);
            $consolidados[] = $consolidado;
        }

        return $consolidados;
    }

    private function seedEntradas($clientes, $bodegas, $catalogos, $consolidados, $usuarios, $startDate, $endDate)
    {
        $entradas = [];
        $clienteList = array_values($clientes);
        $conductorList = array_values($catalogos['conductores']);
        $vehiculoList = array_values($catalogos['vehiculos']);
        $remitentes = $this->seedRemitentes();
        $destinatarios = $this->seedDestinatarios();
        $transportadoraList = array_values($catalogos['transportadoras']);
        $oficinaList = array_values($catalogos['oficinas']);
        $reempacadorList = array_values($catalogos['reempacadores']);
        $codigoList = array_values($catalogos['codigos']);

        for ($indice = 1; $indice <= 48; $indice++) {
            $cliente = $clienteList[($indice - 1) % count($clienteList)];
            $consolidado = $consolidados[($indice - 1) % count($consolidados)];
            $createdAt = $this->dateInRange($startDate, $endDate, $indice);
            $usaComplete = $indice % 7 !== 0;
            $mexicoComplete = $indice % 6 !== 0;
            $confirmed = $indice % 4 !== 0;
            $ocurre = $indice % 3 === 0;
            $transportadora = $transportadoraList[($indice - 1) % count($transportadoraList)];
            $office = collect($oficinaList)->first(function ($item) use ($transportadora) {
                return (int) $item->transportadora_id === (int) $transportadora->id;
            });
            $pesoCliente = 3 + ($indice % 14) * 0.5;
            $largoCliente = 12 + ($indice % 5);
            $anchoCliente = 8 + ($indice % 4);
            $altoCliente = 5 + ($indice % 3);
            $entradaData = [
                'alias' => 'Guía demo ' . $indice,
                'observaciones' => 'Registro generado para pruebas del último mes.',
                'alias_cliente_numero' => $indice % 3 === 0,
                'cliente_id' => $cliente->id,
                'consolidado_id' => $consolidado->id,
                'bodega_id' => $indice % 2 === 0 ? $bodegas['mexico']->id : $bodegas['usa']->id,
                'vuelta' => 1 + ($indice % 4),
                'recibido_at' => $createdAt,
                'recibido_usa_at' => $usaComplete ? $createdAt->copy()->addHours(2) : null,
                'recibido_usa_por' => $usaComplete ? $usuarios['usa']->id : null,
                'recibido_mexico_at' => $mexicoComplete ? $createdAt->copy()->addDays(1) : null,
                'recibido_mexico_por' => $mexicoComplete ? $usuarios['mexico']->id : null,
                'conductor_id' => $conductorList[($indice - 1) % count($conductorList)]->id,
                'vehiculo_id' => $vehiculoList[($indice - 1) % count($vehiculoList)]->id,
                'cruce_at' => $createdAt->copy()->addDays(1),
                'reempacador_id' => $reempacadorList[$indice % count($reempacadorList)]->id,
                'codigor_id' => $codigoList[$indice % count($codigoList)]->id,
                'reempacado_at' => $createdAt->copy()->addDays(2),
                'remitente_id' => $remitentes[($indice - 1) % count($remitentes)]->id,
                'destinatario_id' => $destinatarios[($indice - 1) % count($destinatarios)]->id,
                'transportadora_id' => $transportadora->id,
                'oficina_id' => $ocurre ? $office->id : null,
                'modalidad_entrega' => $ocurre ? 'ocurre' : 'domicilio',
                'destinatario_confirmado' => $confirmed,
                'destinatario_confirmado_at' => $confirmed ? $createdAt->copy()->addDays(3) : null,
                'destinatario_confirmado_por' => $confirmed ? $usuarios['supervisor']->id : null,
                'peso_cliente' => $pesoCliente,
                'largo_cliente' => $largoCliente,
                'ancho_cliente' => $anchoCliente,
                'alto_cliente' => $altoCliente,
                'volumen_cliente' => $largoCliente * $anchoCliente * $altoCliente,
                'peso_usa' => $usaComplete ? $pesoCliente + 0.2 : null,
                'largo_usa' => $usaComplete ? $largoCliente : null,
                'ancho_usa' => $usaComplete ? $anchoCliente : null,
                'alto_usa' => $usaComplete ? $altoCliente : null,
                'volumen_usa' => $usaComplete ? $largoCliente * $anchoCliente * $altoCliente : null,
                'control_usa_tipo' => $usaComplete ? ($indice % 2 === 0 ? 'pesar_medir' : 'solo_pesar') : null,
                'control_usa_completado_at' => $usaComplete ? $createdAt->copy()->addHours(3) : null,
                'control_usa_completado_por' => $usaComplete ? $usuarios['usa']->id : null,
                'peso_mexico' => $mexicoComplete ? $pesoCliente + 0.3 : null,
                'largo_mexico' => $mexicoComplete ? $largoCliente : null,
                'ancho_mexico' => $mexicoComplete ? $anchoCliente : null,
                'alto_mexico' => $mexicoComplete ? $altoCliente : null,
                'volumen_mexico' => $mexicoComplete ? $largoCliente * $anchoCliente * $altoCliente : null,
                'control_mexico_tipo' => $mexicoComplete ? ($indice % 2 === 0 ? 'pesar_medir' : 'solo_pesar') : null,
                'control_mexico_completado_at' => $mexicoComplete ? $createdAt->copy()->addDays(1) : null,
                'control_mexico_completado_por' => $mexicoComplete ? $usuarios['mexico']->id : null,
                'codigo_rastreo' => $confirmed ? 'TRK-DEMO-' . str_pad((string) $indice, 5, '0', STR_PAD_LEFT) : null,
                'codigo_confirmacion' => $confirmed ? 'CONF-' . str_pad((string) $indice, 4, '0', STR_PAD_LEFT) : null,
                'status_salida' => $confirmed && $indice % 5 !== 0 ? 'entregada' : ($confirmed ? 'pendiente_envio' : null),
                'incidente_salida' => $indice % 9 === 0 ? 'Dirección requiere confirmación' : null,
                'notas_salida' => $confirmed ? 'Salida disponible para consulta y seguimiento.' : null,
                'created_by' => $usuarios['documentador']->id,
                'updated_by' => $usuarios['supervisor']->id,
            ];
            $entrada = Entrada::updateOrCreate(['numero' => 'DEMO-GUIA-' . str_pad((string) $indice, 4, '0', STR_PAD_LEFT)], $entradaData);
            $this->setTimestamps($entrada, $createdAt);
            $entradas[] = $entrada;
        }

        return $entradas;
    }

    private function seedRemitentes()
    {
        $remitentes = [];
        foreach (['Remitente Norte', 'Remitente Centro', 'Remitente Sur', 'Remitente Frontera', 'Remitente Industrial', 'Remitente Comercial'] as $indice => $nombre) {
            $remitentes[] = Remitente::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'contacto' => 'Contacto ' . $nombre,
                    'telefono' => '555-03' . str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT),
                    'correo_electronico' => 'remitente' . ($indice + 1) . '@demo.example',
                    'direccion' => 'Calle Remitente ' . ($indice + 1),
                    'codigo_postal' => '6400' . $indice,
                    'ciudad' => 'Ciudad de México',
                    'estado' => 'CDMX',
                    'pais' => 'México',
                    'observaciones' => 'Remitente activo de demostración.',
                    'activo' => true,
                ]
            );
        }
        return $remitentes;
    }

    private function seedDestinatarios()
    {
        $destinatarios = [];
        foreach (['Destinatario Norte', 'Destinatario Centro', 'Destinatario Sur', 'Destinatario Frontera', 'Destinatario Industrial', 'Destinatario Comercial'] as $indice => $nombre) {
            $destinatarios[] = Destinatario::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'contacto' => 'Contacto ' . $nombre,
                    'telefono' => '555-04' . str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT),
                    'correo_electronico' => 'destinatario' . ($indice + 1) . '@demo.example',
                    'direccion' => 'Calle Destinatario ' . ($indice + 1),
                    'codigo_postal' => '4410' . $indice,
                    'referencias' => 'Recepción de lunes a viernes.',
                    'ciudad' => 'Guadalajara',
                    'estado' => 'Jalisco',
                    'pais' => 'México',
                    'observaciones' => 'Destinatario activo de demostración.',
                    'activo' => true,
                ]
            );
        }
        return $destinatarios;
    }

    private function seedConsolidadoMovements($consolidados, $usuario, $startDate, $endDate)
    {
        foreach ($consolidados as $indice => $consolidado) {
            $createdAt = $this->dateInRange($startDate, $endDate, $indice + 3);
            $this->firstMovement(ConsolidadoMovimiento::class, ['consolidado_id' => $consolidado->id, 'tipo' => 'creado'], [
                'usuario_id' => $usuario->id,
                'ocurrido_at' => $createdAt,
                'observacion' => 'Consolidado creado para operación de demostración.',
                'datos' => ['numero' => $consolidado->numero],
            ]);
            if ($consolidado->cerrado) {
                $this->firstMovement(ConsolidadoMovimiento::class, ['consolidado_id' => $consolidado->id, 'tipo' => 'cerrado'], [
                    'usuario_id' => $usuario->id,
                    'ocurrido_at' => $createdAt->copy()->addDays(2),
                    'observacion' => 'Consolidado cerrado en el ciclo demo.',
                    'datos' => ['palets' => $consolidado->palets],
                ]);
            }
        }
    }

    private function seedEntryMovementsAndObservations($entradas, $usuarios, $startDate, $endDate)
    {
        foreach ($entradas as $indice => $entrada) {
            $createdAt = $this->dateInRange($startDate, $endDate, $indice + 1);
            $this->firstMovement(EntradaMovimiento::class, ['entrada_id' => $entrada->id, 'tipo' => 'creada'], [
                'usuario_id' => $usuarios['documentador']->id,
                'ocurrido_at' => $createdAt,
                'bodega_id' => $entrada->bodega_id,
                'conductor_id' => $entrada->conductor_id,
                'vehiculo_id' => $entrada->vehiculo_id,
                'vuelta' => $entrada->vuelta,
                'observacion' => 'Guía creada para demostración.',
                'datos' => ['numero' => $entrada->numero],
            ]);
            if ($entrada->control_usa_tipo) {
                $this->firstMovement(EntradaMovimiento::class, ['entrada_id' => $entrada->id, 'tipo' => $entrada->control_usa_tipo], [
                    'usuario_id' => $usuarios['usa']->id,
                    'ocurrido_at' => $entrada->control_usa_completado_at,
                    'bodega_id' => $entrada->bodega_id,
                    'observacion' => 'Control USA de demostración.',
                    'datos' => ['incidente' => $entrada->id % 9 === 0 ? 'dano_embalaje' : 'sin_incidente'],
                ]);
            }
            if ($entrada->control_mexico_tipo) {
                $this->firstMovement(EntradaMovimiento::class, ['entrada_id' => $entrada->id, 'tipo' => 'recibido_mexico'], [
                    'usuario_id' => $usuarios['mexico']->id,
                    'ocurrido_at' => $entrada->control_mexico_completado_at,
                    'bodega_id' => $entrada->bodega_id,
                    'observacion' => 'Control México de demostración.',
                    'datos' => ['incidente' => $entrada->id % 8 === 0 ? 'faltante' : 'sin_incidente'],
                ]);
            }
            DB::table('entrada_observaciones')->updateOrInsert(
                ['entrada_id' => $entrada->id, 'contenido' => 'Seguimiento demo de la guía.'],
                ['user_id' => $usuarios['supervisor']->id, 'created_at' => $createdAt, 'updated_at' => $createdAt]
            );
        }
    }

    private function firstMovement($modelClass, array $keys, array $values)
    {
        $modelClass::firstOrCreate($keys, $values);
    }

    private function dateInRange($startDate, $endDate, $offset)
    {
        $days = max(1, $startDate->diffInDays($endDate));
        return $startDate->copy()->addDays($offset % ($days + 1))->addHours($offset % 10);
    }

    private function setTimestamps($model, $date)
    {
        $model->created_at = $date;
        $model->updated_at = $date;
        $model->save();
    }

    private function upsertUser($email, $name, $rol, $password, $protectedEmail)
    {
        if ($email === $protectedEmail) {
            throw new \RuntimeException('El usuario protegido no puede usarse como cuenta demo.');
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password, 'rol' => $rol, 'activo' => true]
        );
        $user->update(['name' => $name, 'rol' => $rol, 'activo' => true]);

        return $user;
    }
}
