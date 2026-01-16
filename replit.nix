{ pkgs }: {
  deps = [
    pkgs.nano
    pkgs.tree
    pkgs.php82
    pkgs.php82Extensions.pdo_pgsql
    pkgs.php82Extensions.pgsql
    pkgs.postgresql
  ];
}
