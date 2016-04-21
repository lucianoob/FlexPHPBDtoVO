<?php
date_default_timezone_set('America/Sao_Paulo');

class PHPBDService 
{
	var $username;
	var $password;
	var $server;
	var $port;
	var $databasename;
	var $tablename;
	var $conexao;
	var $query;
	
	function PHPBDService() 
	{
		
	}
	public function openConexao($usuario,$senha,$servidor,$porta)
	{
		$this->username = $usuario;
		$this->password = $senha;
		$this->server = $servidor;
		$this->port = $porta;
		$this->conexao = mysql_connect($this->server . ":" . $this->port, $this->username, $this->password);
		if (!$this->conexao) 
		{
			die('Não foi poss�vel realizar a conexão com o servidor: ' . mysql_error());
		}	
		return true;
	}
	public function listarBancoDados($usuario,$senha,$servidor,$porta)
	{
		$this->openConexao($usuario,$senha,$servidor,$porta);
		$this->query = "show databases";
		$result = mysql_query($this->query);
		$rows = array();
		$i = 0;
		while($row = mysql_fetch_object($result))
		{
			$rows[$i] = $row;
			$i++;
		}
		$this->closeConexao();
		return $rows;
	}
	public function listarTabelas($usuario,$senha,$servidor,$porta,$bancoDados)
	{
		$this->openConexao($usuario,$senha,$servidor,$porta);
		$this->databasename = $bancoDados;
		if (!mysql_select_db($this->databasename, $this->conexao)) 
		{
			die ('Não foi possível conectar com o banco de dados: ' . mysql_error());
		}
		$this->query = "show tables";
		$result = mysql_query($this->query);
		$rows = array();
		$i = 0;
		while($row = mysql_fetch_object($result))
		{
			$rows[$i] = $row;
			$i++;
		}
		$this->closeConexao();
		return $rows;
	}
	public function listarCampos($usuario,$senha,$servidor,$porta,$bancoDados,$tabela)
	{
		$this->openConexao($usuario,$senha,$servidor,$porta);
		$this->databasename = $bancoDados;
		$this->tablename = $tabela;
		if (!mysql_select_db($this->databasename, $this->conexao)) 
		{
			die ('Não foi possível conectar com o banco de dados: ' . mysql_error());
		}
		$this->query = "SHOW FULL COLUMNS FROM ".$this->tablename;
		$result = mysql_query($this->query);
		$rows = array();
		$i = 0;
		while($row = mysql_fetch_object($result))
		{
			$rows[$i] = $row;
			$i++;
		}
		$this->closeConexao();
		return $rows;
	}
	public function listarComentarioTabela($usuario,$senha,$servidor,$porta,$bancoDados,$tabela)
	{
		$this->openConexao($usuario,$senha,$servidor,$porta);
		$this->databasename = $bancoDados;
		$this->tablename = $tabela;
		if (!mysql_select_db($this->databasename, $this->conexao)) 
		{
			die ('Não foi possível conectar com o banco de dados: ' . mysql_error());
		}
		$this->query = "SHOW TABLE STATUS LIKE '".$this->tablename."'";
		$result = mysql_query($this->query);
		$row = mysql_fetch_object($result);
		$this->closeConexao();
		return $row;
	}
	public function existeArquivo($arquivo)
	{
		return file_exists('./'.$arquivo);
	}
	public function salvarArquivo($arquivo, $conteudo)
	{
		$file_open = fopen('./'.$arquivo,'w'); 
	    if(!$file_open)
	    { 
	    	return false;
	    } 
	    fwrite($file_open, $conteudo); 
	    fclose($file_open); 
		return $arquivo;
	}
	public function conteudoArquivo($arquivo)
	{
		$array = array();
		$conteudo = file_get_contents('./'.$arquivo);
		$array[0] = $conteudo;
		$array[1] = $arquivo; 
		$array[2] = date ("d/m/Y H:i:s.", filemtime('./'.$arquivo)); 
		return $array;
	}
	public function metodosArquivo($arquivo)
	{
		$array = array();
		$conteudo = file_get_contents('./'.$arquivo);
		$pubx = explode("public function ", $conteudo);
		for($i=1; $i<count($pubx); $i++)
		{
			$metx = explode("(", $pubx[$i]);
			$array[$i-1] = $metx[0];
		}
		return $array;
	}
	public function closeConexao()
	{
		if(!mysql_close($this->conexao))
		{
			die ('Não foi possível fechar a conexão com o banco de dados: ' . mysql_error());
		}
		return true;
	}
}

?>
