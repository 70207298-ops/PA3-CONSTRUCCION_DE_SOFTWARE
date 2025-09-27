<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$title = $title ?? 'Pet Happy Store';
?><!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { background:#f8fafc; }
      .navbar-brand { font-weight:700; }
      .card { box-shadow: 0 6px 20px rgba(0,0,0,.06); border:0; }
      .form-label { font-weight:600; }
      .table thead th { background:#0d6efd; color:#fff; }
      .badge-status { font-size:.85rem; }
      .flash { position:sticky; top:0; z-index:1080; }
    </style>
  </head>
  <body>
