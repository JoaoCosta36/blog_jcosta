<?php

header('Content-Type: text/html; charset=UTF-8');

include "db.php";
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if(!isset($_POST['mensagem'])) { header("Location: index.php"); exit; }

$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['user_id'];
$mensagem = $conn->real_escape_string($_POST['mensagem']);

$conn->query("INSERT INTO comments(post_id,user_id,mensagem) VALUES($post_id,$user_id,'$mensagem')");

header("Location: post.php?id=$post_id");