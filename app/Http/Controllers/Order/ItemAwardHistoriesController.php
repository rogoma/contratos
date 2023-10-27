<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use App\Models\ItemAwardHistory;

class ItemAwardHistoriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $index_permissions = ['admin.item_award_histories.index',
                            'orders.item_award_histories.index',
                            'process_orders.item_award_histories.index',
                            'derive_orders.item_award_histories.index',
                            'plannings.item_award_histories.index'];
        $create_permissions = ['admin.item_award_histories.create',
                            'orders.item_award_histories.create',
                            'plannings.item_award_histories.create'];
        $update_permissions = ['admin.item_award_histories.update',
                            'orders.item_award_histories.update',
                            'plannings.item_award_histories.update'];

        $this->middleware('checkPermission:'.implode(',',$index_permissions))->only('index'); // Permiso para index 
        $this->middleware('checkPermission:'.implode(',',$create_permissions))->only(['create', 'store']);   // Permiso para create
        $this->middleware('checkPermission:'.implode(',',$update_permissions))->only(['edit', 'update']);   // Permiso para update
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // Chequeamos permisos del usuario en caso de no ser de la dependencia solicitante
        if(!$request->user()->hasPermission(['admin.item_award_histories.index','process_orders.item_award_histories.index',
        'derive_orders.item_award_histories.index','plannings.item_award_histories.index']) &&
        $item->order->dependency_id != $request->user()->dependency_id){
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }

        // Definimos la ruta para volver a la visualizacion del pedido
        if( $request->user()->hasPermission(['plannings.item_award_histories.show']) == TRUE ){
            $orders_route = route('plannings.show', $item->order_id);
        }else{
            $orders_route = route('orders.show', $item->order_id);
        }

        $item_award_histories = $item->itemAwardHistories;
        return view('order.item_award_histories.index', compact('item', 'item_award_histories', 'orders_route'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // Chequeamos permisos del usuario en caso de no ser de la dependencia solicitante
        if(!$request->user()->hasPermission(['admin.item_award_histories.create', 'plannings.item_award_histories.create']) &&
        $item->order->dependency_id != $request->user()->dependency_id){
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }

        $budget_request_providers = $item->order->budgetRequestProviders;
        return view('order.item_award_histories.create', compact('item', 'budget_request_providers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $item_id)
    {
        $item = Item::find($item_id);
        $types = $request->input('type');
        $dncp_pac_ids = $request->input('dncp_pac_id');
        $budget_request_providers = $request->input('budget_request_provider');
        $amounts = $request->input('amount');
        for ($i=0; $i < count($types); $i++) { 
            $item_award_history = new ItemAwardHistory;
            $item_award_history->item_id = $item->id;
            $item_award_history->dncp_pac_id = empty($dncp_pac_ids[$i]) ? NULL : str_replace('.', '', $dncp_pac_ids[$i]);
            $item_award_history->budget_request_provider_id = $budget_request_providers[$i];
            $item_award_history->amount = str_replace(['Gs. ', '.'], '', $amounts[$i]);
            $item_award_history->creator_dependency_id = $request->user()->dependency_id;
            $item_award_history->creator_user_id = $request->user()->id;
            $item_award_history->save();
        }

        $request->session()->flash('success', 'Se han agregado exitosamente los precios referenciales.');
        return response()->json(['status' => 'success', 'code' => 200], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // Chequeamos permisos del usuario en caso de no ser de la dependencia solicitante
        if(!$request->user()->hasPermission(['admin.item_award_histories.update', 'plannings.item_award_histories.update']) &&
        $item->order->dependency_id != $request->user()->dependency_id){
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }

        // obtenemos los precios referenciales cargados por otras dependencias
        $other_dependencies = $item->itemAwardHistories()
            ->where('creator_dependency_id', '!=', $request->user()->dependency_id)->get();
        // obtenemos los precios referenciales cargados por la dependencia actual
        $item_award_histories = $item->itemAwardHistories()
            ->where('creator_dependency_id', $request->user()->dependency_id)->get();
        $budget_request_providers = $item->order->budgetRequestProviders;
        return view('order.item_award_histories.update', compact('item', 'other_dependencies', 'item_award_histories', 'budget_request_providers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $item_id)
    {
        $item = Item::find($item_id);
        $before_ids = empty($request->input('id')) ? array() : (array)$request->input('id');
        $types = $request->input('type');
        $dncp_pac_ids = $request->input('dncp_pac_id');
        $budget_request_providers = $request->input('budget_request_provider');
        $amounts = $request->input('amount');

        // Los registros cargados anteriormente por la dependencia actual
        // que fueron eliminados en la vista, los eliminamos en la bd
        $deleted_items = ItemAwardHistory::where('item_id', $item_id)
                        ->where('creator_dependency_id', $request->user()->dependency_id)
                        ->whereNotIn('id', $before_ids)->get()->pluck('id');
        ItemAwardHistory::destroy($deleted_items);
        
        for ($i=0; $i < count($types); $i++) { 
            // Los registros cargados anteriormente los modificamos
            if(is_array($before_ids) && $i < count($before_ids)){
                $item_award_history = ItemAwardHistory::find($before_ids[$i]);
                $item_award_history->modifier_user_id = $request->user()->id;
            }else{
            // Para los nuevos precios referenciales creamos nuevos registros
                $item_award_history = new ItemAwardHistory;
                $item_award_history->creator_user_id = $request->user()->id;
            }

            $item_award_history->item_id = $item->id;
            $item_award_history->dncp_pac_id = empty($dncp_pac_ids[$i]) ? NULL : str_replace('.', '', $dncp_pac_ids[$i]);
            $item_award_history->budget_request_provider_id = $budget_request_providers[$i];
            $item_award_history->amount = str_replace(['Gs. ', '.'], '', $amounts[$i]);
            $item_award_history->creator_dependency_id = $request->user()->dependency_id;
            $item_award_history->save();
        }

        $request->session()->flash('success', 'Se han modificado exitosamente los precios referenciales.');
        return response()->json(['status' => 'success', 'code' => 200], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // Chequeamos permisos del usuario en caso de no ser de la dependencia solicitante
        if(!$request->user()->hasPermission(['admin.item_award_histories.delete']) &&
        ($item->order->dependency_id != $request->user()->dependency_id && $request->user()->hasPermission(['orders.item_award_histories.delete'])) ){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        // Quitamos todos los precios referenciales del item
        foreach ($item->itemAwardHistories as $item_award_history) {
            $item_award_history->delete();
        }
        
        $request->session()->flash('success', 'Se han eliminado los precios referenciales del ítem.');
        return response()->json(['status' => 'success', 'code' => 200], 200);
    }
}
