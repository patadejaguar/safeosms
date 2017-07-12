#!/bin/sh

read -p "Nombre de la Empresa:" NEMPRESA
read -p "Slogan de la Empresa:" SLOGANEMPRESA
read -p "Direccion de la Empresa:" DEMPRESA
read -p "Datos Legales de la Empresa:" DLEGALEMPRESA

VFECHA=`date +%F`

for file in *.xml; do
BNAM=${file#*.};

cp $file res/$BNAM.$VFECHA.xml

sed -i 's/_NOMBRE_DE_LA_EMPRESA_;/$NEMPRESA;/g' $file;
sed -i 's/_SLOGAN_DE_LA_EMPRESA_;/$SLOGANEMPRESA;/g' $file;
sed -i 's/_DIRECCION_DE_LA_EMPRESA_;/$DEMPRESA;/g' $file;
sed -i 's/_DATOS_LEGALES_DE_LA_EMPRESA_;/$DLEGALEMPRESA;/g' $file;

done


exit 0
