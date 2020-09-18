<?php 
	namespace App\Models;

	use MF\Model\Model;

	class Tweet extends Model{
		private $id;
		private $id_usuario ;
		private $tweet;
		private $data;

		public function __get($attr){
			return $this->$attr;
		}

		public function __set($attr, $valor){
			$this->$attr = $valor;
		}

		public function salvar(){
			$query = "insert into tweets(id_usuario, tweet)values(:id_usuario, :tweet)";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
			$stmt->bindValue(':tweet', $this->__get('tweet'));
			$stmt->execute();

			return $this;			
		}

		public function getAll(){
			$query = "select tweets.id, tweets.id_usuario, tweets.tweet, DATE_FORMAT(tweets.data, '%d/%m/%Y %H:%i') as data, usuarios.nome from tweets left join usuarios on (tweets.id_usuario = usuarios.id) where tweets.id_usuario = :id_usuario order by tweets.data desc";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
			$stmt->execute();

			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
	}
?>