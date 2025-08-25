.PHONY: up

up:
	@echo "➡️  Aktualizuję repozytorium..."
	@git checkout main
	@git pull origin main

	@echo "➡️  Restartuję Apache..."
	@sudo systemctl restart apache2

	@echo "➡️  Uruchamiam MySQL..."
	@sudo service mysql start

	@IP=$$(hostname -I | tr ' ' '\n' | grep -E '^192\.168\.' | head -n 1); \
	echo "➡️  Updatuje tabele..."; \
	php bin/console create-tables; \
	echo "➡️  Aktualizuję pytania..."; \
	cd quiz && git checkout main && git pull origin main && cd ..; \
	echo "➡️  Pobieram obrazki..."; \
	php bin/console download-images; \
	if [ -n "$$IP" ]; then \
	    echo "✅ Apache działa. Strona dostępna pod: http://$$IP/quiz"; \
	else \
	    echo "⚠️ Nie udało się znaleźć adresu LAN. Sprawdź 'hostname -I'."; \
	fi
