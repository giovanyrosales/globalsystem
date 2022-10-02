<?php

namespace App\Http\Controllers\Backend\Roles;

use App\Http\Controllers\Controller;
use App\Models\P_Departamento;
use App\Models\P_UsuarioDepartamento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //retorna vista de "Permisos" en sisdebar
    public function index(){
        $roles = Role::all()->pluck('name', 'id');

        return view('backend.admin.rolesypermisos.permisos', compact('roles'));
    }

    // muestra tabla de usuarios del sistema
    public function tablaUsuarios(){
        $usuarios = Usuario::orderBy('id', 'ASC')->get();

        return view('backend.admin.rolesypermisos.tabla.tablapermisos', compact('usuarios'));
    }

    // crear nuevo usuario
    public function nuevoUsuario(Request $request){

        // verificar que usuario no este registrado
        if(Usuario::where('usuario', $request->usuario)->first()){
            return ['success' => 1];
        }

        $u = new Usuario();
        $u->nombre = $request->nombre;
        $u->usuario = $request->usuario;
        $u->password = bcrypt($request->password);
        $u->activo = 1;

        if ($u->save()) {
            $u->assignRole($request->rol);
            return ['success' => 2];
        } else {
            return ['success' => 3];
        }
    }

    // obtener información de un usuario
    public function infoUsuario(Request $request){
        if($info = Usuario::where('id', $request->id)->first()){

            $roles = Role::all()->pluck('name', 'id');

            $idrol = $info->roles->pluck('id');

            return ['success' => 1,
                'info' => $info,
                'roles' => $roles,
                'idrol' => $idrol];

        }else{
            return ['success' => 2];
        }
    }

    // editar un usuario
    public function editarUsuario(Request $request){

        if(Usuario::where('id', $request->id)->first()){

            // verificar que usuario no este repetido
            if(Usuario::where('usuario', $request->usuario)
                ->where('id', '!=', $request->id)->first()){
                return ['success' => 1];
            }

            $usuario = Usuario::find($request->id);
            $usuario->nombre = $request->nombre;
            $usuario->usuario = $request->usuario;
            $usuario->activo = $request->toggle;

            if($request->password != null){
                $usuario->password = bcrypt($request->password);
            }

            //elimina el rol existente y agrega el nuevo.
            $usuario->syncRoles($request->rol);

            $usuario->save();

            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    // crear un nuevo Rol
    public function nuevoRol(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // verificar si existe el rol
        if(Role::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Role::create(['name' => $request->nombre]);

        return ['success' => 2];
    }

    // crear nuevos permisos
    public function nuevoPermisoExtra(Request $request){

        // verificar si existe el permiso
        if(Permission::where('name', $request->nombre)->first()){
            return ['success' => 1];
        }

        Permission::create(['name' => $request->nombre, 'description' => $request->descripcion]);

        return ['success' => 2];
    }

    // borrar permiso global, a todos los roles que lo contenga
    public function borrarPermisoGlobal(Request $request){

        // buscamos el permiso el cual queremos eliminar
        Permission::findById($request->idpermiso)->delete();

        return ['success' => 1];
    }


    //******************  ASIGNACIÓN DE USUARIO A DEPARTAMENTO  *************************************************

    // retorna vista para asignar usuario a un departamento
    public function indexUsuarioDepartamento(){

        $usuarios = Usuario::orderBy('nombre')->get();
        $departamentos = P_Departamento::orderBy('nombre')->get();

        return view('backend.admin.rolesypermisos.usuariodepartamento.vistausuariodepartamento', compact('usuarios', 'departamentos'));
    }

    // retorna tabla de usuarios asignados a un departamento
    public function tablaUsuarioDepartamento(){

        $listado = DB::table('p_usuario_departamento AS pud')
            ->join('usuario AS u', 'pud.id_usuario', '=', 'u.id')
            ->join('p_departamento AS pd', 'pud.id_departamento', '=', 'pd.id')
            ->select('pud.id', 'u.nombre', 'u.usuario', 'pd.nombre AS nombredepa')
            ->orderBy('u.nombre')
            ->get();

        return view('backend.admin.rolesypermisos.usuariodepartamento.tablausuariodepartamento', compact('listado'));
    }

    // crear nueva asignación de usuario a departamento
    public function nuevoUsuarioDepartamento(Request $request){

        $regla = array(
            'usuario' => 'required',
            'departamento' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // verificar si existe el usuario
        if(P_UsuarioDepartamento::where('id_usuario', $request->usuario)->first()){
            return ['success' => 1];
        }

        $dato = new P_UsuarioDepartamento();
        $dato->id_usuario = $request->usuario;
        $dato->id_departamento = $request->departamento;
        $dato->save();

        return ['success' => 2];
    }

    // información de usuario asignado a departamento
    public function informacionUsuarioDepartamento(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = P_UsuarioDepartamento::where('id', $request->id)->first()){

            $depar = P_Departamento::orderBy('nombre')->get();

            $infoUsuario = Usuario::where('id', $info->id_usuario)->first();
            $nombre = $infoUsuario->nombre;

            return ['success' => 1, 'info' => $info, 'nombre' => $nombre, 'depa' => $depar];
        }else{
            return ['success' => 2];
        }
    }

    // editar asignación de usuario a departamento
    public function editarUsuarioDepartamento(Request $request){

        $regla = array(
            'id' => 'required',
            'departamento' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_UsuarioDepartamento::where('id', $request->id)->first()){

            P_UsuarioDepartamento::where('id', $request->id)->update([
                'id_departamento' => $request->departamento,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


}
