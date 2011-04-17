CREATE TABLE timeRequirement
(
timeRequirementID INT NOT NULL,
timeRequirementShortDescription VARCHAR(100) NOT NULL,
timeRequirementLongDescription TEXT,
PRIMARY KEY (timeRequirementID)
);
CREATE TABLE area
(
areaSlug VARCHAR(100) NOT NULL,
areaName VARCHAR(100) NOT NULL,
areaURL VARCHAR(255) NOT NULL,
areaDescription TEXT,
areaParentSlug VARCHAR(100) NOT NULL,
timeRequirementID INT NOT NULL,
PRIMARY KEY (areaSlug),
FOREIGN KEY (timeRequirementID) REFERENCES timeRequirement(timeRequirementID)
);
CREATE TABLE skill
(
skillTag VARCHAR(50) NOT NULL,
skillName VARCHAR(100) NOT NULL,
PRIMARY KEY (skillTag)
);
CREATE TABLE areaSkill
(
areaSlug VARCHAR(100) NOT NULL,
skillTag VARCHAR(50) NOT NULL,
PRIMARY KEY (areaSlug, skillTag),
FOREIGN KEY (areaSlug) REFERENCES area(areaSlug),
FOREIGN KEY (skillTag) REFERENCES skill(skillTag)
);
CREATE TABLE metric(
metricDate DATE NOT NULL,
areaSlug VARCHAR(100) NOT NULL,
metricQty INT NOT NULL,
PRIMARY KEY (metricDate, areaSlug),
FOREIGN KEY (areaSlug) REFERENCES area(areaSlug)
);
CREATE TABLE locale
(
localeID VARCHAR(5) NOT NULL,
localeName VARCHAR(100) NOT NULL,
PRIMARY KEY (localeID)
);
CREATE TABLE metricLocale
(
metricDate DATE NOT NULL,
areaSlug VARCHAR(100) NOT NULL,
localeID VARCHAR(5) NOT NULL,
metricLocaleQty INT NOT NULL,
PRIMARY KEY (metricDate, areaSlug, localeID),
FOREIGN KEY (areaSlug) REFERENCES area(areaSlug),
FOREIGN KEY (localeID) REFERENCES locale(localeID)
);
CREATE TABLE user
(
username VARCHAR(100) NOT NULL,
userPassword CHAR(128) NOT NULL,
userEmail VARCHAR(320) NOT NULL,
PRIMARY KEY (username)
);
CREATE TABLE invite
(
token CHAR(40) NOT NULL,
invitee VARCHAR(100) NOT NULL,
inviter VARCHAR(100) NOT NULL,
PRIMARY KEY (token, invitee, inviter),
FOREIGN KEY (invitee, inviter) REFERENCES user.username
);
INSERT INTO locale (`localeID`, `localeName`) VALUES ('ms-bn', 'Malay - Brunei');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('mr', 'Marathi');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('mn', 'Mongolian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ml', 'Malayalam');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('mk', 'FYRO Macedonia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('mi', 'Maori');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('lv', 'Latvian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('lt', 'Lithuanian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('lo', 'Lao');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('la', 'Latin');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ks', 'Kashmiri');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ko', 'Korean');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('kn', 'Kannada');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('km', 'Khmer');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('kk', 'Kazakh');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ja', 'Japanese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('it-it', 'Italian - Italy');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('it-ch', 'Italian - Switzerland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('is', 'Icelandic');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('id', 'Indonesian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('hy', 'Armenian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('hu', 'Hungarian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('hr', 'Croatian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('hi', 'Hindi');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('he', 'Hebrew');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('gu', 'Gujarati');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('gn', 'Guarani - Paraguay');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('gd-ie', 'Gaelic - Ireland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('gd', 'Gaelic - Scotland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fr-lu', 'French - Luxembourg');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fr-fr', 'French - France');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fr-ch', 'French - Switzerland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fr-ca', 'French - Canada');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fr-be', 'French - Belgium');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fo', 'Faroese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fi', 'Finnish');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('fa', 'Farsi - Persian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('eu', 'Basque');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('et', 'Estonian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-ve', 'Spanish - Venezuela');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-uy', 'Spanish - Uruguay');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('Salva', 'Spanish -');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-py', 'Spanish - Paraguay');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-pr', 'Spanish - Puerto Rico');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-pe', 'Spanish - Peru');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-pa', 'Spanish - Panama');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-ni', 'Spanish - Nicaragua');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-mx', 'Spanish - Mexico');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-hn', 'Spanish - Honduras');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-gt', 'Spanish - Guatemala');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-es', 'Spanish - Spain (Traditional)');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-ec', 'Spanish - Ecuador');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-do', 'Spanish - Dominican Republic');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-cr', 'Spanish - Costa Rica');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-co', 'Spanish - Colombia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-cl', 'Spanish - Chile');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-bo', 'Spanish - Bolivia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('es-ar', 'Spanish - Argentina');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-za', 'English - Southern Africa');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-us', 'English - United States');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-tt', 'English - Trinidad');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-ph', 'English - Phillippines');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-nz', 'English - New Zealand');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-jm', 'English - Jamaica');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-in', 'English - India');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-ie', 'English - Ireland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-gb', 'English - Great Britain');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-cb', 'English - Caribbean');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-ca', 'English - Canada');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-bz', 'English - Belize');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('en-au', 'English - Australia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('el', 'Greek');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('dv', 'Divehi; Dhivehi; Maldivian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('de-lu', 'German - Luxembourg');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('de-li', 'German - Liechtenstein');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('de-de', 'German - Germany');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('de-ch', 'German - Switzerland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('de-at', 'German - Austria');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('da', 'Danish');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('cy', 'Welsh');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('cs', 'Czech');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ca', 'Catalan');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('bs', 'Bosnian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('bo', 'Tibetan');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('bn', 'Bengali - India');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('bg', 'Bulgarian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('be', 'Belarusian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('az-az', 'Azeri - Latin');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('as', 'Assamese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-ye', 'Arabic - Yemen');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-tn', 'Arabic - Tunisia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-sy', 'Arabic - Syria');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-sa', 'Arabic - Saudi Arabia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-qa', 'Arabic - Qatar');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-om', 'Arabic - Oman');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-ma', 'Arabic - Morocco');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-ly', 'Arabic - Libya');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-lb', 'Arabic - Lebanon');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-kw', 'Arabic - Kuwait');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-jo', 'Arabic - Jordan');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-iq', 'Arabic - Iraq');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-eg', 'Arabic - Egypt');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-dz', 'Arabic - Algeria');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-bh', 'Arabic - Bahrain');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ar-ae', 'Arabic - United Arab Emirates');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('am', 'Amharic');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('af', 'Afrikaans');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ms-my', 'Malay - Malaysia');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('mt', 'Maltese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('my', 'Burmese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ne', 'Nepali');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('nl-be', 'Dutch - Belgium');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('nl-nl', 'Dutch - Netherlands');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('no-no', 'Norwegian - Bokml');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('or', 'Oriya');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('pa', 'Punjabi');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('pl', 'Polish');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('pt-br', 'Portuguese - Brazil');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('pt-pt', 'Portuguese - Portugal');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('rm', 'Raeto-Romance');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ro', 'Romanian - Romania');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ro-mo', 'Romanian - Moldova');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ru', 'Russian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ru-mo', 'Russian - Moldova');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sa', 'Sanskrit');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sb', 'Sorbian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sd', 'Sindhi');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('si', 'Sinhala; Sinhalese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sk', 'Slovak');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sl', 'Slovenian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('so', 'Somali');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sq', 'Albanian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sr-sp', 'Serbian - Latin');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sv-fi', 'Swedish - Finland');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sv-se', 'Swedish - Sweden');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('sw', 'Swahili');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ta', 'Tamil');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('te', 'Telugu');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('tg', 'Tajik');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('th', 'Thai');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('tk', 'Turkmen');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('tn', 'Setsuana');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('tr', 'Turkish');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ts', 'Tsonga');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('tt', 'Tatar');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('uk', 'Ukrainian');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('ur', 'Urdu');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('uz-uz', 'Uzbek - Latin');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('vi', 'Vietnamese');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('xh', 'Xhosa');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('yi', 'Yiddish');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('zh-cn', 'Chinese - China');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('zh-hk', 'Chinese - Hong Kong SAR');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('zh-mo', 'Chinese - Macau SAR');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('zh-sg', 'Chinese - Singapore');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('zh-tw', 'Chinese - Taiwan');
INSERT INTO `locale` (`localeID`, `localeName`) VALUES ('zu', 'Zulu')