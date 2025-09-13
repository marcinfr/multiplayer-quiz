.PHONY: up update-repo start-server update-images update-db check-ip

up: update-repo start-server update-images update-db check-ip

update-repo:
	@echo "➡️  Aktualizuję repozytorium gry..."
	@git checkout main
	@git pull origin main
	@echo "➡️  Aktualizuję repozytorium pytań..."; \
	cd quiz && git checkout main && git pull origin main && cd ..;

start-server:
	@echo "➡️  Restartuję Apache..."
	@sudo systemctl restart apache2
	@echo "➡️  Uruchamiam MySQL..."
	@sudo service mysql start

update-images:
	@echo "➡️  Pobieram obrazki..."
	@php bin/console download-images

update-db:
	@echo "➡️  Updatuje tabele..."
	@php bin/console create-tables


check-ip:
	@IP=$$(hostname -I | tr ' ' '\n' | grep -E '^192\.168\.' | head -n 1); \
	if [ -n "$$IP" ]; then \
	    echo "✅ Apache działa. Strona dostępna pod: http://$$IP/quiz"; \
	else \
	    echo "⚠️ Nie udało się znaleźć adresu LAN. Sprawdź 'hostname -I'."; \
	fi
