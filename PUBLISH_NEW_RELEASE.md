# New release
Een release start je met een git-tag. Zodra je een tag vX.Y.Z naar GitHub pusht, start de workflow en maakt hij de zip, update.xml, changelog.xml en de GitHub Release.

Als voorbeeld: de laatste versie was 0.0.28, dus de volgende is 0.0.29:

```bash
git pull
git tag v0.0.29
git push origin v0.0.29
```

git pull eerst: de workflow commit zelf naar master (update.xml, changelog.xml en het versienummer in de manifest). Je lokale kopie loopt daardoor achter, en zonder pull kan je tag op een verouderde commit terechtkomen.
Tag expliciet pushen: een gewone git push stuurt tags niet mee. Je moet git push origin v0.0.29 doen, of git push --tags.
v voor het versienummer: de tag moet het formaat vX.Y.Z hebben, anders start de workflow niet.

Je kunt de workflow ook handmatig starten via Actions → Build & Release → Run workflow. Vul bij versie 0.0.29 in, zonder v. De tag-methode is wel de nette manier.
