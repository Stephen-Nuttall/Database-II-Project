package com.example.movies;

import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.movies.R;
import com.example.movies.models.Responses;
import com.example.movies.network.ApiService;
import com.example.movies.network.RetrofitClient;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class item6 extends AppCompatActivity {
    
    ApiService apiService;
    Spinner sectionSpinner;
    EditText editEmail, editPassword, editStudentId;
    TextView tvMessage, tvCurrentSemester, tvCurrentYear;
    Button btnAddTa;
    String semester, year;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.item6);
        apiService = RetrofitClient.getInstance().create(ApiService.class);
        initializeViews();
        setButtonListeners();
    }

    private void initializeViews() {
        sectionSpinner = findViewById(R.id.section_spinner);
        editEmail = findViewById(R.id.editEmail);
        editPassword = findViewById(R.id.editPassword);
        editStudentId = findViewById(R.id.editStudentId);
        tvCurrentYear = findViewById(R.id.tvCurrentYear);
        tvCurrentSemester = findViewById(R.id.tvCurrentSemester);
        tvMessage = findViewById(R.id.tvMessage);
        btnAddTa = findViewById(R.id.btnAddTa);
        loadSectionDropdown();
    }

    private void setButtonListeners() {
        btnAddTa.setOnClickListener(v -> addTa());
    }

    private void loadSectionDropdown() {
        apiService.getSections("get_sections").enqueue(new Callback<Responses>() {
            @Override
            public void onResponse(Call<Responses> call, Response<Responses> response) {
                //Log.d("API_RESULT", response.body().toString());
                if (response.isSuccessful() && response.body() != null && response.body().isSuccess()) {
                    List<String> sections = new ArrayList<>();
                    sections.add("--Select a section--");
                    sections.addAll(response.body().sections);
                    year = response.body().year;

                    // Set Semester and Year to TextViews
                    if (response.body().semester != null) {
                        tvCurrentSemester.setText("Semester: " + response.body().semester);
                    }
                    if (response.body().year != null) {
                        tvCurrentYear.setText("Year: " + response.body().year);
                    }

                    ArrayAdapter<String> adapter = new ArrayAdapter<>(
                            item6.this,
                            android.R.layout.simple_spinner_item,
                            sections
                    );
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                    sectionSpinner.setAdapter(adapter);
                } else {
                    Toast.makeText(item6.this, "Failed to load sections", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Responses> call, Throwable t) {
                Toast.makeText(item6.this, "API error: " + t.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    private void addTa() {
        String email = editEmail.getText().toString().trim();
        String password = editPassword.getText().toString().trim();
        String studentId = editStudentId.getText().toString().trim();
        String section = sectionSpinner.getSelectedItem().toString();


        if (!isFormValid(email, password, studentId, section)) return;

        Call<Responses> call = apiService.addTa(email, password, studentId, section);
        call.enqueue(new Callback<Responses>() {
            @Override
            public void onResponse(Call<Responses> call, Response<Responses> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Responses result = response.body();
                    int color = result.isSuccess() ? Color.GREEN : result.isReply() ? Color.BLACK : Color.RED;
                    showMessage(result.getMessage(), color);
                } else {
                    showMessage("Unexpected server response.", Color.RED);
                }
            }

            @Override
            public void onFailure(Call<Responses> call, Throwable t) {
                showMessage("Failed to connect: " + t.getMessage(), Color.RED);
            }
        });
    }

    private boolean isFormValid(String email, String password, String studentId, String section) {
        if (email.isEmpty()) {
            showMessage("Please enter email.", Color.RED);
            return false;
        }
        if (password.isEmpty()) {
            showMessage("Please enter password.", Color.RED);
            return false;
        }
        if (studentId.isEmpty()) {
            showMessage("Please enter student ID.", Color.RED);
            return false;
        }
        if (section.equals("--Select a section--")) {
            showMessage("Please select section.", Color.RED);
            return false;
        }
        return true;
    }

    private void showMessage(String message, int color) {
        tvMessage.setTextColor(color);
        tvMessage.setText(message);
    }
}
