#!/bin/sh

VFECHA=`date +%F`

for file in *.xml; do
BNAM=${file#*.};

##cp $file res/$BNAM.$VFECHA.xml
## Limpiar fields
sed -i "s/ALIGN='LEFT'  CELLCLASS='FIELDS' TEXTCLASS='FIELDS'/CELLCLASS='FL'/g" $file;
sed -i "s/ALIGN='CENTER'  CELLCLASS='FIELDS' TEXTCLASS='FIELDS'/CELLCLASS='FC'/g" $file;
sed -i "s/ALIGN='RIGHT'  CELLCLASS='FIELDS' TEXTCLASS='FIELDS'/CELLCLASS='FR'/g" $file;

sed -i "s/ALIGN='LEFT'  CELLCLASS='FIELDS'/CELLCLASS='FL'/g" $file;
sed -i "s/ALIGN='CENTER'  CELLCLASS='FIELDS'/CELLCLASS='FC'/g" $file;
sed -i "s/ALIGN='RIGHT'  CELLCLASS='FIELDS'/CELLCLASS='FR'/g" $file;

sed -i "s/ALIGN='LEFT'  TEXTCLASS='FIELDS'/CELLCLASS='FL'/g" $file;
sed -i "s/ALIGN='CENTER'  TEXTCLASS='FIELDS'/CELLCLASS='FC'/g" $file;
sed -i "s/ALIGN='RIGHT'  TEXTCLASS='FIELDS'/CELLCLASS='FR'/g" $file;

##LIMPIAR COLUMNAS Y FOOTERS

sed -i "s/ALIGN='CENTER'  CELLCLASS='GROUP_HEADER'/CELLCLASS='GHC'/g" $file;
sed -i "s/ALIGN='CENTER'  CELLCLASS='GROUP_FOOTER'/CELLCLASS='GHC'/g" $file;


sed -i "s/ALIGN='LEFT'  CELLCLASS='GROUP_HEADER'/CELLCLASS='GHL'/g" $file;
sed -i "s/ALIGN='LEFT'  CELLCLASS='GROUP_FOOTER'/CELLCLASS='GHL'/g" $file;

sed -i "s/ALIGN='RIGHT'  CELLCLASS='GROUP_HEADER'/CELLCLASS='GHR'/g" $file;
sed -i "s/ALIGN='RIGHT'  CELLCLASS='GROUP_FOOTER'/CELLCLASS='GHR'/g" $file;


#LIMPIAR REMANENTES
sed -i "s/CELLCLASS='GROUP_HEADER'/CELLCLASS='GHC'/g" $file;
sed -i "s/CELLCLASS='GROUP_FOOTER'/CELLCLASS='GFC'/g" $file;

sed -i "s/TEXTCLASS='BOLDRED'//g" $file;
sed -i "s/ALIGN='RIGHT'//g" $file;
sed -i "s/ALIGN='CENTER'//g" $file;
sed -i "s/ALIGN='LEFT'//g" $file;

sed -i "s/TEXTCLASS='GROUP_FOOTER'//g" $file;
sed -i "s/TEXTCLASS='GROUP_HEADER'//g" $file;

sed -i "s/TEXTCLASS='HEADER'//g" $file;
sed -i "s/TEXTCLASS='FOOTER'//g" $file;

sed -i "s/CELLCLASS='HEADER'/CELLCLASS='GHL'/g" $file;
sed -i "s/CELLCLASS='FOOTER'/CELLCLASS='GHL'/g" $file;

sed -i "s/CELLCLASS='FIELDS'//g" $file;

sed -i "s/WIDTH='1%'//g" $file;



sed -i "s/CELLCLASS='FL'//g" $file;
done


exit 0
