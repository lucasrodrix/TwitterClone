<?php 
	namespace App\Models;

	use MF\Model\Model;

	class Usuario extends Model{
		private $id;
		private $nome;
		private $email;
		private $senha;

		public function __get($attr){
			return $this->$attr;
		}

		public function __set($attr, $valor){
			$this->$attr = $valor;
		}

		public function salvar(){
			$query = "insert into usuarios(nome, email, senha)values(:nome, :email, :senha)";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':nome', $this->__get('nome'));
			$stmt->bindValue(':email', $this->__get('email'));
			$stmt->bindValue(':senha', $this->__get('senha'));
			$stmt->execute();

			return $this;
		}

		public function validarCadastro(){
			$valido = true;

			if(strlen($this->__get('nome')) < 3 || strlen($this->__get('email')) < 3 || strlen($this->__get('senha')) < 3){
				$valido = false;
			}

			return $valido;
		}

		public function getUsuarioPorEmail(){
			$query = "select nome, email from usuarios where email = :email";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':email', $this->__get('email'));
			$stmt->execute();
			
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function autenticar(){
			$query = "select id, nome, email from usuarios where email = :email and senha = :senha";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':email', $this->__get('email'));
			$stmt->bindValue(':senha', $this->__get('senha'));
			$stmt->execute();
			
			$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

			if($usuario['id'] != ''  && $usuario['nome'] != ''){
				$this->__set('id', $usuario['id']);
				$this->__set('nome', $usuario['nome']);
			}

			return $this;
		}

		public function getAll(){
			$query = "select id, nome, email, 
			(select count(*) from usuarios_seguidores where usuarios_seguidores.id_usuario = :id_usuario and usuarios_seguidores.id_usuario_seguindo = usuarios.id ) as seguindo_sn 
			from usuarios where nome like :nome and id != :id_usuario";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();
			
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function followUser($id_usuario_seguindo){
			$query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguindo)values(:id_usuario, :id_usuario_seguindo)";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
			$stmt->execute();

			return true;
		}

		public function unfollowUser($id_usuario_seguindo){
			$query = "delete from usuarios_seguidores where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
			$stmt->execute();

			return true;
		}
	}
?>