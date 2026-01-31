{ pkgs }: {
  deps = [
    pkgs.vim
    pkgs.zip
    pkgs.rsync
    pkgs.php82Packages.composer-local-repo-plugin
    pkgs.nano
    pkgs.tree
    pkgs.php82
    pkgs.php82Extensions.pdo_pgsql
    pkgs.php82Extensions.pgsql
    pkgs.postgresql
  ];
}
