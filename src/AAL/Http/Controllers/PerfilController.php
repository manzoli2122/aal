<?php

namespace  Manzoli2122\AAL\Http\Controllers;

use Illuminate\Http\Request;
//use Manzoli2122\AAL\Models\Perfil;
//use Manzoli2122\AAL\Models\Permissao; 
use Illuminate\Support\Facades\Config; 

class PerfilController extends StandardController
{
 
        protected $model;   
        protected $permissao; 
        protected $user; 
        protected $name = "Perfil";    
        protected $view = "autorizacao::perfis";    
        protected $route = "perfis";
        
        
        //public function __construct(Perfil $perfil , Permissao $permissao){
        public function __construct(){

            $usuarioModelName = Config::get('auth.providers.users.model');
            $this->user = new $usuarioModelName();

            $perfilModelName = Config::get('aal.perfil');
            $this->model = new $perfilModelName();

            $permissaoModelName = Config::get('aal.permissao');
            $this->permissao = new $permissaoModelName();
            
            //$this->model = $perfil;
            //$this->permissao = $permissao;

            $this->middleware('permissao:perfis');
                     
            
        }






        public function usuarios($id)
        {            
            $model = $this->model->find($id);
            $users = $model->usuarios()->get();
            return view("{$this->view}.usuarios", compact('model','users'));
        }





        public function usuariosAdd($id)
        {            
            $model = $this->model->find($id);
            $users =$this->user->usuarios_sem_perfil($id);
            return view("{$this->view}.usuarios-add", compact('model','users'));
        }


        
        public function deleteUser($id,$userId)
        {            
            $model = $this->model->find($id);            
            $model->detachUsuario($userId); 
            return redirect()->route("{$this->route}.usuarios" ,$id)->with(['success' => 'Usuarios Removido com sucesso']);
        }



        public function usuariosAddPerfil(Request $request , $id)
        {            
            $model = $this->model->find($id);            
            $model->attachUsuario($request->get('users'));            
            return redirect()->route("{$this->route}.usuarios" ,$id)->with(['success' => 'Usuarios vinculados com sucesso']);
        }


        public function pesquisarUsuarios(Request $request , $id)
        {            
            $dataForm = $request->except('_token');
            $model = $this->model->find($id);
            $users = $model->usuarios()->where('users.name','LIKE', "%{$dataForm['key']}%")
                                       ->orWhere('users.email',$dataForm['key'])->get();           
            return view("{$this->view}.usuarios", compact('model', 'dataForm', 'users'));
        }





























        public function permissoes($id)
        {            
            $model = $this->model->find($id);           
            return view("{$this->view}.permissoes", compact('model'));
        }




        public function permissoesAdd($id)
        {            
            $model = $this->model->find($id);
            $permissoes = $this->permissao->permissos_sem_perfil($id);    
            return view("{$this->view}.permissoes-add", compact('model','permissoes'));
        }


        
        public function deletePermissao($id,$permissaoId)
        {            
            $model = $this->model->find($id);            
            $model->detachPermissao($permissaoId); 
            return redirect()->route("{$this->route}.permissoes" ,$id)->with(['success' => 'Permissa Removida com sucesso']);
        }



        public function permissoesAddPerfil(Request $request , $id)
        {            
            $model = $this->model->find($id);            
            $model->attachPermissao($request->get('permissoes'));            
            return redirect()->route("{$this->route}.permissoes" ,$id)->with(['success' => 'Permissoes vinculados com sucesso']);
        }


        public function pesquisarPemissoes(Request $request , $id)
        {            
            $dataForm = $request->except('_token');
            $model = $this->model->find($id);
            $permissoes = $model->permissoes()->where('permissoes.nome','LIKE', "%{$dataForm['key']}%")
                                       ->paginate($this->totalPage);           
            return view("{$this->view}.permissoes", compact('model', 'dataForm', 'permissoes'));
        }
        
        

        
}
