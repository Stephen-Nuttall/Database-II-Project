package com.example.movies;

import android.graphics.Color;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.example.movies.R;
import com.example.movies.models.Responses;
import com.example.movies.network.ApiService;
import com.example.movies.network.RetrofitClient;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class item3 extends AppCompatActivity {
    
    ApiService apiService;
    EditText editStudentId, editPassword;
    TextView tvMessage;
    Button btnViewStudentRecords;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.item3);
        apiService = RetrofitClient.getInstance().create(ApiService.class);
        initializeViews();
        setButtonListeners();
    }

    private void initializeViews() {
        editStudentId = findViewById(R.id.editStudentId);
        editPassword = findViewById(R.id.editPassword);
        tvMessage = findViewById(R.id.tvMessage);
        btnViewStudentRecords = findViewById(R.id.btnViewStudentRecords);
    }

    private void setButtonListeners() {
        btnViewStudentRecords.setOnClickListener(v -> viewStudentRecords());
    }

    private void viewStudentRecords() {
        String studentId = editStudentId.getText().toString().trim();
        String password = editPassword.getText().toString().trim();

        if (!isFormValid(studentId, password)) return;

        Call<Responses> call = apiService.viewStudentRecords(studentId, password);
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

    private boolean isFormValid(String studentId, String password) {
        if (studentId.isEmpty()) {
            showMessage("Please enter student ID.", Color.RED);
            return false;
        }
        if (password.isEmpty()) {
            showMessage("Please enter password.", Color.RED);
            return false;
        }
        return true;
    }

    private void showMessage(String message, int color) {
        tvMessage.setTextColor(color);
        tvMessage.setText(message);
    }
}
