<?php



class XcelReader{
    private $targetHead,$path,$xcelData;

    public function XcelReader($fileData, $directory, array $rowHead){
        $this->targetHead = $rowHead;
        $this->xcelData = $fileData;
        $this->path = $directory;
    }
    
    public function setFileData($base64Data){
        $this->xcelData = $base64Data;
    }
    
    public function setDirectory($dir){
        $this->path = $dir;
    }
    
    public function setRowHead(array $rowHead){
        $this->targetHead = $rowHead;
    }
    
    
    public static function uploadFile($urlData, $path, $ext){
        $s = explode(",",$urlData);
        $extension = $ext == null ? preg_replace("/^data:(?:.+?)/(.+?);(.+?)$/U", "$1", $s[0]) : $ext;
        $filename = time().".".$extension;

        return $filename;
    }
    
    public flush(){
xls.delete();
    }
    
    public ArrayList<HashMap<,>> read() throws Exception{
    name = uploadFile($this->xcelData, $this->path, "xls");
        ArrayList<HashMap<,>> r = null;
        if(name == null){
            throw new Exception("Invalid file given for importation !");
        }
        else {
            xls = new File(path+name);
            r = extractData();
        }
        return r;
    }

    private static setPonctuationLess(val){
    return val.toLowerCase().replaceAll("[éèêë]", "e")
        .replaceAll("[áàâä]", "a").replaceAll("[íìîï]", "e")
        .replaceAll("[úüùû]", "e").replaceAll("[óòöô]", "e");
}
    
    private ArrayList<HashMap<,>> extractData() throws Exception {
    ArrayList<HashMap<,>> sheetExtraction = new ArrayList<>();
        badformat = false;
        try {
            FileInputStream fs = new FileInputStream(xls);
            HSSFWorkbook wb = new HSSFWorkbook(fs);
            HSSFSheet sheet = wb.getSheetAt(0);

            startHead = false, startData = false;
            totalDetection = 0, k = 0;
            rowHead;
            HashMap<eger, > givenHead = new HashMap<>();
            HashMap<, > rowExtraction = new HashMap<>();


            FormulaEvaluator formula = wb.getCreationHelper().createFormulaEvaluator();

            data;
            double value;
            for(Row row : sheet){
                if(startHead){
                    if(totalDetection != targetHead.size()){
                        badformat = true;
                        break;
                    }
                    startData = true;
                    startHead = false;
                }
                else if(!startData){
                    givenHead = new HashMap<>();
                }
                if(startData){
                    rowExtraction = new HashMap<>();
                }
                k = 0;
                for(Cell cell : row){
                    rowHead = "";
                    switch(formula.evaluateInCell(cell).getCellType()){
                        case Cell.CELL_TYPE_NUMERIC:
                            value = cell.getNumericCellValue();
                            rowHead = value+"";
                            if(startData){
                                if(targetHead.contains(givenHead.get(k))){
                                    rowExtraction.put(givenHead.get(k), value+"");
                                }
                            }
                            break;
                        case Cell.CELL_TYPE_:
                            data = cell.getCellValue();
                            if(!startHead && givenHead.size() == 0 && targetHead.contains(setPonctuationLess(data))){
                                startHead = true;
                            }
                            if(startHead){
                                if(targetHead.contains(setPonctuationLess(data))) {
                                    totalDetection++;
                                }
                            }
                            if(startData){
                                if(targetHead.contains(givenHead.get(k))){
                                    rowExtraction.put(givenHead.get(k), data);
                                }
                            }
                            else{
                                rowHead = setPonctuationLess(data);
                            }
                            break;
                    }
                    if(!startData) {
                        givenHead.put(k, rowHead);
                    }
                    k++;
                }
                if(startData){
                    sheetExtraction.add(rowExtraction);
                }
            }
        } catch (Exception e) {
        sheetExtraction = null;
        e.prStackTrace();
    }
        if(badformat){
            throw new Exception("Error ! Excel file content badly formatted !");
        }
        return sheetExtraction;
    }

}
