
html_theme = "sphinx_rtd_theme"
html_theme_path = [ "_themes" ]
html_style = 'my'

os.system("mkdir _themes; cd _themes; git init; git remote add -f origin https://github.com/GrandLTU/sphinx_rtd_theme.git; git config core.sparsecheckout true; echo 'sphinx_rtd_theme' > .git/info/sparse-checkout; git reset --hard; git pull origin ongr;")
