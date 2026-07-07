# Group logo

The fixed red logo supplied during this build is already present at:

`public/assets/brand/group-logo-red.svg`

For a future approved replacement that has the same copied layout styling, run the normaliser from the project root:

```bash
php scripts/normalise-logo-svg.php /path/to/original-logo.svg public/assets/brand/group-logo-red.svg
```

The command removes the copied browser-layout styles and replaces the outer `fill="none"` value with Scouts Wales red (`#ED3F23`). Use only approved artwork from the Scouts Brand Centre.
