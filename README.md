# EcoRide

**EcoRide** est une application de covoiturage éco-responsable permettant à des chauffeurs et passagers de s’organiser, de réserver et de gérer des trajets.

## 🚀 Lancer l’application en local

### Prérequis :
- Docker & Docker Compose installés

### Installation :

```bash
git clone https://github.com/TON-PSEUDO/ecoride.git
cd ecoride
docker-compose up --build
## 🔗 Accès à l'application
- URL locale : http://localhost

## 👤 Connexions possibles

| Rôle          | Email                     | Mot de passe   |
|---------------|---------------------------|----------------|
| Admin         | monadmin@test.com         | admin123 |
| Employé       | evamiolard@gmail.com      | Password |
| Utilisateur   | jean.dupont@example.com   | motdepasse 

## 🛠 Structure Git

- `main` : branche stable (prod)
- `develop` : branche de développement
- `usX-*` : une branche par user story
  - Ex : `us6-login`, `us7-creer-compte`, `us13-admin`, etc.
