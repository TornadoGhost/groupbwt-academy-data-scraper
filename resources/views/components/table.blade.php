<div id="{{ $id ?? 'table' }}" class="table-responsive">
    <div class="d-flex justify-content-between align-items-center p-2">
        <div class="d-flex align-items-center">
            <span>Show</span>
            <select class="form-select d-inline mx-2" id="paginationPerPage">
            </select>
            <span>entries</span>
        </div>
        <div class="d-flex align-items-center">
            <label class="form-label d-inline m-0 font-weight-normal" for="searchInTable">Search:</label>
            <input class="form-control ml-2" id="searchInTable" type="text">
        </div>
    </div>
    <table class="table table-hover table-striped table-sm m-0 mb-2" style="width:100%">
        <thead class="thead-dark">
        <tr>
            {{ $slot }}
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span id="paginationInformation"></span>
        </div>
        <div>
            <nav aria-label="Table navigation">
                <ul class="pagination pagination-sm m-0" id="tablePagination"></ul>
            </nav>
        </div>
    </div>
</div>
