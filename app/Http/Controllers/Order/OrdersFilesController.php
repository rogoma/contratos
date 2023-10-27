<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrdersFilesController extends Controller
{
    protected $postMaxSize;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $index_permissions = ['admin.files.index',
                            'orders.files.index',
                            'process_orders.files.index',
                            'derive_orders.files.index',
                            'plannings.files.index',
                            'tenders.files.index',
                            'minor_purchases.files.index',
                            'awards.files.index',
                            'exceptions.files.index',
                            'contracts.files.index',
                            'utas.files.index',
                            'legal_advices.files.index',
                            'comites.files.index',
                            'coordinations.files.index',
                            'dgafs.files.index',
                            'documentals.files.index'];
        $create_permissions = ['admin.files.create',
                            'orders.files.create',
                            'derive_orders.files.create',
                            'plannings.files.create',
                            'tenders.files.create',
                            'minor_purchases.files.create',
                            'awards.files.create',
                            'exceptions.files.create',
                            'contracts.files.create',
                            'utas.files.create',
                            'legal_advices.files.create',
                            'comites.files.create',
                            'coordinations.files.create',
                            'dgafs.files.create',
                            'documentals.files.create'];
        $show_permissions = ['admin.files.show',
                            'orders.files.show',
                            'process_orders.files.show',
                            'derive_orders.files.show',
                            'plannings.files.show',
                            'tenders.files.show',
                            'minor_purchases.files.show',
                            'awards.files.show',
                            'exceptions.files.show',
                            'contracts.files.show',
                            'utas.files.show',
                            'legal_advices.files.show',
                            'comites.files.show',
                            'coordinations.files.show',
                            'dgafs.files.show',
                            'documentals.files.show'];
        $download_permissions = ['admin.files.download',
                            'orders.files.download',
                            'process_orders.files.download',
                            'derive_orders.files.download',
                            'plannings.files.download',
                            'tenders.files.download',
                            'minor_purchases.files.download',
                            'awards.files.download',
                            'exceptions.files.download',
                            'contracts.files.download',
                            'utas.files.download',
                            'legal_advices.files.download',
                            'comites.files.download',
                            'coordinations.files.download',
                            'dgafs.files.download',
                            'documentals.files.download'];
        $update_permissions = ['admin.files.update',
                            'orders.files.update',
                            'derive_orders.files.update',
                            'plannings.files.update',
                            'tenders.files.update',
                            'minor_purchases.files.update',
                            'awards.files.update',
                            'exceptions.files.update',
                            'contracts.files.update',
                            'utas.files.update',
                            'legal_advices.files.update',
                            'comites.files.update',
                            'coordinations.files.update',
                            'dgafs.files.update',
                            'documentals.files.update'];

        $this->middleware('checkPermission:'.implode(',',$index_permissions))->only('index'); // Permiso para index 
        $this->middleware('checkPermission:'.implode(',',$create_permissions))->only(['create', 'store']);   // Permiso para create
        $this->middleware('checkPermission:'.implode(',',$show_permissions))->only(['show']);   // Permiso para show
        $this->middleware('checkPermission:'.implode(',',$download_permissions))->only(['download']);   // Permiso para download
        $this->middleware('checkPermission:'.implode(',',$update_permissions))->only(['edit', 'update']);   // Permiso para update

        // obtenemos el tamaño permitido de subida de archivos del servidor
        if (is_numeric(ini_get('post_max_size'))) {
            $postMaxSize = ini_get('post_max_size');
        }else{
            $metric = strtoupper(substr(ini_get('post_max_size'), -1));
            $postMaxSize = (int) ini_get('post_max_size');

            switch ($metric) {
                case 'K':
                    $postMaxSize = $postMaxSize * 1024;
                    break;
                case 'M':
                    $postMaxSize = $postMaxSize * 1048576;
                    break;
                case 'G':
                    $postMaxSize = $postMaxSize * 1073741824;
                    break;
                default:
                    $postMaxSize = 8 * 1024 * 1024;
                    break;
            }
        }
        $this->postMaxSize = $postMaxSize;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $post_max_size = $this->postMaxSize;
        return view('order.files.create', compact('order', 'post_max_size'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_filedncp(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $post_max_size = $this->postMaxSize;
        return view('order.files.create_filedncp', compact('order', 'post_max_size'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_filedncp_con(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $post_max_size = $this->postMaxSize;
        return view('order.files.create_filedncp_con', compact('order', 'post_max_size'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_cuadro_compar(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $post_max_size = $this->postMaxSize;
        return view('order.files.create_cuadro_compar', compact('order', 'post_max_size'));
    }


    /**
     * Funcionalidad de agregar de archivo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        
        $rules = array(
            'description' => 'string|required|max:100',
        );
        
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if(!$request->hasFile('file')){
            $validator = Validator::make($request->input(), []);
            $validator->errors()->add('file', 'El campo es requerido, debe ingresar un archivo WORD, PDF o EXCEL.');
            return back()->withErrors($validator)->withInput();
        }

        // chequeamos la extension del archivo subido
        $extension = $request->file('file')->getClientOriginalExtension();
        if(!in_array($extension, array('doc', 'docx', 'pdf', 'xls', 'xlsx'))){
            $validator = Validator::make($request->input(), []); // Creamos un objeto validator
            $validator->errors()->add('file', 'El archivo introducido debe corresponder a alguno de los siguientes formatos: doc, docx, pdf, xls, xlsx.'); // Agregamos el error
            return back()->withErrors($validator)->withInput();
        }

        // Pasó todas las validaciones, guardamos el archivo
        $fileName = time().'-order-file.'.$extension; // nombre a guardar
        // Cargamos el archivo (ruta storage/app/public/files, enlace simbolico desde public/files)
        $path = $request->file('file')->storeAs('public/files', $fileName);

        $file = new File;
        $file->description = $request->input('description');
        $file->file = $fileName;        

        if ($request->user()->dependency->id <> 57){
            $file->file_type = 0;        
        }
        // *** Usuario Asesoría Jurídica  tipo 5 = dictamenes*** 
        if ($request->user()->dependency->id == 57){
            $file->file_type = 5;        
        }
        // *** Usuario Contratos tipo 3 = contratos*** 
        if ($request->user()->dependency->id == 60){
            $file->file_type = 3;        
        }

        // *** Usuario Licitaciones, Compras Menores y Excepciones tipo 7 = Cuadros comparativos*** 
        // if (in_array($request->user()->dependency->id, [61,62,63])){        
        //     // Ver bandera para preguntar si es cuadro comparativo o archivo normal
        //     $file->file_type = 7;
        // }
        // if($request->user()->hasPermission(['admin.orders.update']) || $request->user()->dependency_id == 55){
        // @if (in_array($order->orderState->id, [105]))


        $file->order_id = $order_id;
        $file->order_state_id = $order->actual_state;
        $file->creator_user_id = $request->user()->id;  // usuario logueado
        $file->dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $file->save();

        // Dependiendo del modulo direccionamos a la vista del pedido
        if($request->user()->hasPermission(['plannings.files.create'])){
            return redirect()->route('plannings.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }elseif($request->user()->hasPermission(['tenders.files.create'])){
            return redirect()->route('tenders.show', $order_id);  
        }elseif($request->user()->hasPermission(['minor_purchases.files.create'])){
            return redirect()->route('minor_purchases.show', $order_id);         
        }elseif($request->user()->hasPermission(['awards.files.create'])){
            return redirect()->route('awards.show', $order_id);
        }elseif($request->user()->hasPermission(['exceptions.files.create'])){
            return redirect()->route('exceptions.show', $order_id);
        }elseif($request->user()->hasPermission(['contracts.files.create'])){
            return redirect()->route('contracts.show', $order_id); 
        }elseif($request->user()->hasPermission(['utas.files.create'])){
            return redirect()->route('utas.show', $order_id);
        }elseif($request->user()->hasPermission(['legal_advices.files.create'])){
            return redirect()->route('legal_advices.show', $order_id);
        }elseif($request->user()->hasPermission(['comites.files.create'])){
            return redirect()->route('comites.show', $order_id); 
        }elseif($request->user()->hasPermission(['coordinations.files.create'])){
            return redirect()->route('coordinations.show', $order_id); 
        }elseif($request->user()->hasPermission(['dgafs.files.create'])){
            return redirect()->route('dgafs.show', $order_id);
        }elseif($request->user()->hasPermission(['documentals.files.create'])){
            return redirect()->route('documentals.show', $order_id);
        }else{
            return redirect()->route('orders.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }
    }

    /**
     * Funcionalidad de agregar de archivo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_filedncp(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        
        $rules = array(
            'description' => 'string|required|max:100',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!$request->hasFile('file')) {
            $validator = Validator::make($request->input(), []);
            $validator->errors()->add('file', 'El campo es requerido, debe ingresar un archivo WORD, PDF o EXCEL.');
            return back()->withErrors($validator)->withInput();
        }

        // chequeamos la extension del archivo subido
        $extension = $request->file('file')->getClientOriginalExtension();
        if (!in_array($extension, array('doc', 'docx', 'pdf', 'xls', 'xlsx'))) {
            $validator = Validator::make($request->input(), []); // Creamos un objeto validator
            $validator->errors()->add('file', 'El archivo introducido debe corresponder a alguno de los siguientes formatos: doc, docx, pdf, xls, xlsx.'); // Agregamos el error
            return back()->withErrors($validator)->withInput();
        }

        // Pasó todas las validaciones, guardamos el archivo
        $fileName = time().'-order-file.'.$extension; // nombre a guardar
        // Cargamos el archivo (ruta storage/app/public/files, enlace simbolico desde public/files)
        $path = $request->file('file')->storeAs('public/files', $fileName);

        $file = new File;
        $file->description = $request->input('description');
        $file->file = $fileName;
        // *** filetype 1 = archivos de reparos // filetype 4 = Addendas ****        
        if ($request->user()->dependency->id == 60) {
            $file->file_type = 4;
        }else{
            $file->file_type = 1;        
        }
        $file->order_id = $order_id;
        $file->order_state_id = $order->actual_state;
        $file->creator_user_id = $request->user()->id;  // usuario logueado
        $file->dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $file->save();

        // Dependiendo del modulo direccionamos a la vista del pedido
        if($request->user()->hasPermission(['plannings.files.create'])){
            return redirect()->route('plannings.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }elseif($request->user()->hasPermission(['tenders.files.create'])){
            return redirect()->route('tenders.show', $order_id);  
        }elseif($request->user()->hasPermission(['minor_purchases.files.create'])){
            return redirect()->route('minor_purchases.show', $order_id);         
        }elseif($request->user()->hasPermission(['awards.files.create'])){
            return redirect()->route('awards.show', $order_id);
        }elseif($request->user()->hasPermission(['exceptions.files.create'])){
            return redirect()->route('exceptions.show', $order_id);
        }elseif($request->user()->hasPermission(['contracts.files.create'])){
            return redirect()->route('contracts.show', $order_id);        
        }elseif($request->user()->hasPermission(['utas.files.create'])){
            return redirect()->route('utas.show', $order_id);
        }elseif($request->user()->hasPermission(['legal_advices.files.create'])){
            return redirect()->route('legal_advices.show', $order_id);
        }elseif($request->user()->hasPermission(['comites.files.create'])){
            return redirect()->route('comites.show', $order_id);            
        }elseif($request->user()->hasPermission(['coordinations.files.create'])){
            return redirect()->route('coordinations.show', $order_id);
        }elseif($request->user()->hasPermission(['dgafs.files.create'])){
            return redirect()->route('dgafs.show', $order_id);
        }elseif($request->user()->hasPermission(['documentals.files.create'])){
            return redirect()->route('documentals.show', $order_id);    
        }else{
            return redirect()->route('orders.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }
    }

    /**
     * Funcionalidad de agregar de archivo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_filedncp_con(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        
        $rules = array(
            'description' => 'string|required|max:100',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!$request->hasFile('file')) {
            $validator = Validator::make($request->input(), []);
            $validator->errors()->add('file', 'El campo es requerido, debe ingresar un archivo WORD, PDF o EXCEL.');
            return back()->withErrors($validator)->withInput();
        }

        // chequeamos la extension del archivo subido
        $extension = $request->file('file')->getClientOriginalExtension();
        if (!in_array($extension, array('doc', 'docx', 'pdf', 'xls', 'xlsx'))) {
            $validator = Validator::make($request->input(), []); // Creamos un objeto validator
            $validator->errors()->add('file', 'El archivo introducido debe corresponder a alguno de los siguientes formatos: doc, docx, pdf, xls, xlsx.'); // Agregamos el error
            return back()->withErrors($validator)->withInput();
        }

        // Pasó todas las validaciones, guardamos el archivo
        $fileName = time().'-order-file.'.$extension; // nombre a guardar
        // Cargamos el archivo (ruta storage/app/public/files, enlace simbolico desde public/files)
        $path = $request->file('file')->storeAs('public/files', $fileName);

        $file = new File;
        $file->description = $request->input('description');
        $file->file = $fileName;
        // *** filetype 6 = archivos de consultas DNCP ****        
        $file->file_type = 6;        
        $file->order_id = $order_id;
        $file->order_state_id = $order->actual_state;
        $file->creator_user_id = $request->user()->id;  // usuario logueado
        $file->dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $file->save();

        // Dependiendo del modulo direccionamos a la vista del pedido
        if($request->user()->hasPermission(['plannings.files.create'])){
            return redirect()->route('plannings.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }elseif($request->user()->hasPermission(['tenders.files.create'])){
            return redirect()->route('tenders.show', $order_id);  
        }elseif($request->user()->hasPermission(['minor_purchases.files.create'])){
            return redirect()->route('minor_purchases.show', $order_id);         
        }elseif($request->user()->hasPermission(['awards.files.create'])){
            return redirect()->route('awards.show', $order_id);
        }elseif($request->user()->hasPermission(['exceptions.files.create'])){
            return redirect()->route('exceptions.show', $order_id);
        }elseif($request->user()->hasPermission(['contracts.files.create'])){
            return redirect()->route('contracts.show', $order_id);        
        }elseif($request->user()->hasPermission(['utas.files.create'])){
            return redirect()->route('utas.show', $order_id);
        }elseif($request->user()->hasPermission(['legal_advices.files.create'])){
            return redirect()->route('legal_advices.show', $order_id);
        }elseif($request->user()->hasPermission(['comites.files.create'])){
            return redirect()->route('comites.show', $order_id);            
        }elseif($request->user()->hasPermission(['coordinations.files.create'])){
            return redirect()->route('coordinations.show', $order_id);
        }elseif($request->user()->hasPermission(['dgafs.files.create'])){
            return redirect()->route('dgafs.show', $order_id);
        }elseif($request->user()->hasPermission(['documentals.files.create'])){
            return redirect()->route('documentals.show', $order_id);    
        }else{
            return redirect()->route('orders.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }
    }

    /**
     * Funcionalidad de agregar cuadro comparativo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_cuadro_compar(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        
        $rules = array(
            'description' => 'string|required|max:100',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!$request->hasFile('file')) {
            $validator = Validator::make($request->input(), []);
            $validator->errors()->add('file', 'El campo es requerido, debe ingresar un archivo WORD, PDF o EXCEL.');
            return back()->withErrors($validator)->withInput();
        }

        // chequeamos la extension del archivo subido
        $extension = $request->file('file')->getClientOriginalExtension();
        if (!in_array($extension, array('doc', 'docx', 'pdf', 'xls', 'xlsx'))) {
            $validator = Validator::make($request->input(), []); // Creamos un objeto validator
            $validator->errors()->add('file', 'El archivo introducido debe corresponder a alguno de los siguientes formatos: doc, docx, pdf, xls, xlsx.'); // Agregamos el error
            return back()->withErrors($validator)->withInput();
        }

        // Pasó todas las validaciones, guardamos el archivo
        $fileName = time().'-order-file.'.$extension; // nombre a guardar
        // Cargamos el archivo (ruta storage/app/public/files, enlace simbolico desde public/files)
        $path = $request->file('file')->storeAs('public/files', $fileName);

        $file = new File;
        $file->description = $request->input('description');
        $file->file = $fileName;
        // *** filetype 7 = cuadro comparativos
        $file->file_type = 7;

        $file->order_id = $order_id;
        $file->order_state_id = $order->actual_state;
        $file->creator_user_id = $request->user()->id;  // usuario logueado
        $file->dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $file->save();

        // Dependiendo del modulo direccionamos a la vista del pedido
        if($request->user()->hasPermission(['plannings.files.create'])){
            return redirect()->route('plannings.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }elseif($request->user()->hasPermission(['tenders.files.create'])){
            return redirect()->route('tenders.show', $order_id);  
        }elseif($request->user()->hasPermission(['minor_purchases.files.create'])){
            return redirect()->route('minor_purchases.show', $order_id);         
        }elseif($request->user()->hasPermission(['awards.files.create'])){
            return redirect()->route('awards.show', $order_id);
        }elseif($request->user()->hasPermission(['exceptions.files.create'])){
            return redirect()->route('exceptions.show', $order_id);
        }elseif($request->user()->hasPermission(['contracts.files.create'])){
            return redirect()->route('contracts.show', $order_id);        
        }elseif($request->user()->hasPermission(['utas.files.create'])){
            return redirect()->route('utas.show', $order_id);
        }elseif($request->user()->hasPermission(['legal_advices.files.create'])){
            return redirect()->route('legal_advices.show', $order_id);
        }elseif($request->user()->hasPermission(['comites.files.create'])){
            return redirect()->route('comites.show', $order_id);            
        }elseif($request->user()->hasPermission(['coordinations.files.create'])){
            return redirect()->route('coordinations.show', $order_id);
        }elseif($request->user()->hasPermission(['dgafs.files.create'])){
            return redirect()->route('dgafs.show', $order_id);
        }elseif($request->user()->hasPermission(['documentals.files.create'])){
            return redirect()->route('documentals.show', $order_id);
        }else{
            return redirect()->route('orders.show', $order_id)->with('success', 'Archivo agregado correctamente');
        }
        
    }

    /**
     * Funcionalidad para descargar archivo.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $file_id)
    {
        $file = File::findOrFail($file_id);
        // Eliminamos caracteres especiales de la descripcion y pasamos como nombre del archivo
        $name = Str::slug($file->description);
        //Separamos nombre y extensión de la descripción del archivo
        $filename = explode(".", $file->file);
        //Obteemos extensión del archivo
        $extension = $filename[1];        
        return Storage::download('public/files/'.$file->file, $name .'.'. $extension);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $file_id)
    {
        // Chequeamos que el usuario actual disponga de permisos de eliminacion
        if(!$request->user()->hasPermission(['admin.files.delete', 'orders.files.delete',
            'process_orders.files.delete', 'derive_orders.files.delete', 
            'plannings.files.delete','tenders.files.delete','minor_purchases.files.delete',
            'exceptions.files.delete','awards.files.delete','contracts.files.delete',
            'utas.files.delete','legal_advices.files.delete','comites.files.delete',
            'coordinations.files.delete','dgafs.files.delete','documentals.files.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $file = File::find($file_id);
        // Eliminamos el archivo
        Storage::delete('public/files/'.$file->file);
        
        // Eliminamos el registro del archivo
        $file->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado el archivo ', 'code' => 200], 200);
    }
}