include ./Makefile.local

dest=$(plugins_dir)/buzzscrape

.PHONY: clean install

default: all

all: clean install

install:
	mkdir -p "$(dest)"
	$(if $(dest),cp -Rf *.php css js img "$(dest)" && echo "Files installed in $(dest)",echo "Nothing to install")

clean:
	$(if $(dest), cd "$(dest)" && rm -f *.php && rm -rf css js img && echo "Cleaned $(dest)",echo "Nothing to clean")

