# WebExcess.Form Package for Neos CMS #
[![Latest Stable Version](https://poser.pugx.org/webexcess/form/v/stable)](https://packagist.org/packages/webexcess/form)
[![License](https://poser.pugx.org/webexcess/form/license)](https://packagist.org/packages/webexcess/form)

This package extends the default typo3/form plugin with different presets (Bootstrap, Foundation, Material Design, Floating Label). Further it handels translation for labels and placeholders.

With the WebExcess.Form Package you also have the possibility to save the entered formdata to the database.

## Compatibility and Maintenance
WebExcess.Form is currently being maintained for Neos 2.3 LTS

| Neos Version | WebExcess.Form Version | Maintained |
|--------------|------------------------|------------|
| Neos 2.3 LTS | 1.x                    | YES        |

## Installation
```
composer require webexcess/form
```

## Configuration
*(Die Dokumentation ist noch nicht ganz komplett)*

### Page
- **label** (string)
Identifier for `<f:translate id="{your.label}" />`

### Form-Tag
- **containerClassAttribute** (string) *[Default: '‘]*
Fügt dem Form-Tag eine Class hinzu
- **multipartForm** (boolean) *[Default: FALSE]*
Render das Attribut `enctype="multipart/form-data"`
- **renderSubmitButton** (boolean) *[Default: TRUE]*
Wenn auf `TRUE` wird am Ende des Formulars die Navigation (Submit-Button) angezeigt
- **buttonContainerClass** (string) *[Default: 'form-navigation‘]*
Class für die Button-Navigation
- **previousButtonClass** (string) *[Default: 'btn btn-cancel‘]*
Class für den Previous-Button
- **nextButtonClass** (string) *[Default: 'btn btn-primary‘]*
Class für den Next-Button
- **submitButtonClass** (string) *[Default: 'btn btn-primary‘]*
Class für den Submit-Button
- **renderLabel** (boolean) *[Default: TRUE]*

### Form-Elemente
- **containerClassAttribute** (string) *[Default: 'clearfix‘]*
Class für den Container um das Input-Feld- **errorClassAttribute** (string) *[Default: 'error']*

### SingleLineText
- **elementClassAttribute** (string) *[Default: '‘]*
Class für das Input-Feld
- **type** (string) *[Default: 'text‘]*
Type für das Input-Feld (text, email, number, etc.)
- **placeholder** (boolean) *[Default: FALSE]*
Wenn `TRUE` wird ein Placeholder ausgegeben

### MultiLineText
- **elementClassAttribute** (string) *[Default: '‘]*
Class für das Text-Feld
- **rows** (integer) *[Default: '‘]*
- **cols** (integer) *[Default: '‘]*
- **placeholder** (boolean) *[Default: FALSE]*
Wenn `TRUE` wird ein Placeholder ausgegeben

### WebExcess.Form:Spinner
Fügt ein Nummer-Input hinzu

### WebExcess.Form:Submit
Fügt einen Button an einem beliebigen Ort ein.
- **containerClassAttribute** (string) *[Default: 'clearfix‘]*
- **elementClassAttribute** (string) *[Default: 'btn btn-primary‘]*

### WebExcess.Form:Column
Fügt ein Mehrspalten-Element ein
- **rowClassAttribute** (string) *[Default: 'row‘]*
Class für den Container
- **colClassAttribute** (string) *[Default: 'col-sm-6‘]*
- **colRenderPosition** (string) *[Default: FALSE]*
Für die erste Spalte verwenden sie das Attribut `first`. Für die letzte Spalte `last`.
