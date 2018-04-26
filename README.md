# biodiversity-literature-repository
Exploring the Biodiversity Literature Repository


## How to

The file harvest_ids.php calls Zenodo’s OAI-PMH endpoint http://zenodo.org/oai2d and gets the identifier for each record in the **user-biosyslit** set, outputting one per line, with random pauses every 10 records to give the server a breather. The responses of the OAI-PMH endpoint are cached in the folder **cache**.

The output of this step is then input into harvest_from_list.php, which calls the Zenodo API to retrieve a JSON version of the record and stores it in a CouchDB database.

## Caching and resizing images

http://exeg5le.cloudimg.io

http://exeg5le.cloudimg.io/s/height/200/https://zenodo.org/record/855995/files/figure.png

http://exeg5le.cloudimg.io/s/height/200/https://zenodo.org/record/179079/files/figure.png

http://exeg5le.cloudimg.io/s/height/200/https://zenodo.org/record/179078/files/figure.png

## Cloudant

curl http://localhost:5984/_replicate -H 'Content-Type: application/json' -d '{ "source": "zenodo”, "target": "https://rdmpage:<password>@rdmpage.cloudant.com/zenodo"}'


## Ideas

### Taxonomic name indexing

Find names in caption, link to GBIF hierarchy, provide taxonomic search

### Compare with GBIF

Find maps of species, generate automatic comparison page with GBIF data, see what is missing

### 

## Interesting searches

### Type specimens (lots of specimen codes)

### Scale bar (potential for image mining to get dimensions)

### People

“Spider” comes up with a taxonomist, could add faces to machine learning tasks

## To do

Parse reference lists and convert to CSL for eventual import into CSL-playground

Search interface for articles (include image preview)

Parse action for museum specimens

## Examples to look at further

# FIGURE 2. Geographic distribution of Gephyrocharax major, a Cis-Andean species, from Amazon River basin in Bolivia and Peru.

Looks like lots of records in Plaza treatment are not extracted https://www.gbif.org/species/2353348